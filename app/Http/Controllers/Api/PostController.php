<?php

namespace App\Http\Controllers\Api;

// import model post
use App\Models\Post;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        // get all posts
        $posts = Post::latest()->paginate(5);

        // return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $posts);
    }
    // save data
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 442);
        }

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // create post
        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // return response
        return new PostResource(true, 'Data Post berhasil ditambahkan!', $post);
    }
    // show data
    public function show($id)
    {
        // find post by ID
        $post = Post::find($id);

        // return single post as a resource
        return new PostResource(true, 'Detail data Post!', $post);
    }
    // update data
    public function update(Request $request, $id)
    {
        // define validation rules
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // find post by ID
        $post = Post::find($id);

        // check if image is not empty
        if ($request->hasFile('image')) {
            // upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/' . basename($post->image));

            // update post with new image
            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content,
            ]);
        } else {
            // update post without image
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }

        // return respone
        return new PostResource(true, 'Data post berhasil diubah!', $post);
    }
    // delete data
    public function destroy($id)
    {
        //find post by ID
        $post = Post::find($id);

        // delete image
        Storage::delete('public/posts/' . basename($post->image));

        // delete post
        $post->delete();

        return new PostResource(true, 'Data post berhasil dihapus!', null);
    }
}
