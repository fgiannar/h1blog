<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Post;
use App\Events\PostCreated;

class PostController extends Controller
{
    /**
     * Retrieve posts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        $posts = new Post();
        if ($request->has('author_id')) {
            $posts = $posts->where('author_id', $request->get('author_id'));
        }
        if ($request->has('tag')) {
            $posts = $posts->tagged($request->get('tag'));
        }
        if ($request->has('published') && $request->get('published')) {
            $posts = $posts->published();
        }
        if ($request->has('order')) {
            $order = $request->get('order') == 'desc' ? 'desc' : 'asc';
            $posts = $posts->orderBy('published_at', $order);
        }

        return response()->json($posts->with('author')->get());
    }

    /**
     * Create a new post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function create(Request $request)
    {
        $this->validate($request, $this->getValidation());

        $this->sanitize($request);

        $post = Post::create($request->all());

        // we replicate here so that response is not affected by whatever
        // attributes are added to the post model during the event execution.
        event(new PostCreated($post->replicate()));

        return response()->json($post, 201);
    }

    /**
     * Update the specified post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $this->validate($request, $this->getValidation());

        $this->sanitize($request);

        $post->update($request->all());

        return response()->json($post);
    }

    /**
     * Remove the specified post from storage.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy($id)
    {
        Post::findOrFail($id);
    }

    /**
     * Get the validation for a post create/update request.
     *
     * @return array
     */
    protected function getValidation()
    {
        return [
            'title' => 'required|max:255',
            'body' => 'required',
            'author_id' => 'required|exists:users,id',
            'tags' => 'required|array',
            'published_at' => 'sometimes|date_format:Y-m-d H:i:s|before_or_equal:now'
        ];
    }

    /**
     * Sanitize request input.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function sanitize(Request $request)
    {
        $input = $request->all();

        $input['title'] = filter_var($input['title'], FILTER_SANITIZE_STRING);
        $input['body'] = clean($input['body']); // Sanitize it using HTML_Purifier
        foreach ($input['tags'] as &$tag) {
            $tag = filter_var($tag, FILTER_SANITIZE_STRING);
        }

        $request->replace($input);
    }
}
