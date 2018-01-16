<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'body', 'tags', 'author_id', 'published_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'published_at',
    ];


    /**
     * Mutator to convert tags attribute to string with commas at the beginning and the end.
     * Example: ["sports", "basket"] would be converted to ,sports,basket,
     * The commas at the beginning and the end are used to facilitate db searches.
     *
     * @param array $tags
     *
     */
    public function setTagsAttribute($tags)
    {
        $tagsStr = '';
        foreach ($tags as $tag) {
            $tagsStr .= trim($tag) . ',';
        }
        $this->attributes['tags'] = strlen($tagsStr) !== false ? ',' . $tagsStr : null;
    }

    /**
     * Accessor to convert tags attribute to array.
     * @param array $tags
     * @return array
     *
     */
    public function getTagsAttribute($tags)
    {
        return $tags ? explode(',', trim($tags, ',')) : [];
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    /**
     * Scope a query to only include published posts.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {

        return $query->whereNotNull('published_at');
    }

    /**
     * Scope a query to only include posts with given tags.
     *
     * @param array $tags
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTagged($query, $tags)
    {
        $tags = explode(',', $tags);
        if (empty($tags)) {
            return $query;
        }

        $tag = array_shift($tags);
        $query = $query->where('tags', 'like', '%,' . $tag . ',%');

        foreach ($tags as $tag) {
            $query = $query->where('tags', 'like', '%,' . $tag . ',%');
        }

        return $query;
    }
}
