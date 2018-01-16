<body>
    <p>Hi admin,</p>
    <p>A new blog post was just created. See details below:</p>
    <p>Title: <strong>{{ $post->title }}</strong></p>
    <p>Author: <strong>{{ $post->author->name }}</strong></p>
    <p>Published: <strong>{{ $post->published_at ? $post->published_at->toDayDateTimeString() : 'Not published' }}</strong></p>
</body>
