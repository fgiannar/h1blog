# h1blog
A dead simple blog api created with Lumen.
Lumen is a micro-framework by Laravel optimized for APIs as it is lighter and faster than Laravel.
More about Lumen at https://lumen.laravel.com/.

# Installation
- Clone the repo on your local machine
```git clone https://github.com/fgiannar/h1blog.git```
- Move to project's directory
```cd h1blog```
- Copy .env_sample  to .env and update all MAIL_* envs to match your smtp credentials and ADMIN_MAIL to your email.
```cp .env_sample .env```
- Build image and start containers:
```docker-compose build```
```docker-compose up -d```
- Create database tables and populate them with dummy data:
```docker-compose exec app php artisan:migrate --seed```

API's endpoints can be now accessed at baseurl: http://0.0.0.0:8009/api/v1

# Endpoints
## **GET api/v1/posts:** Fetch all blog posts.
Optional parameters:
**author_id:** int. Fetches the blog posts of a given user, eg GET *api/v1/posts?author_id=1*

**order:** enum, 'asc' or 'desc'. Fetches the blog posts sorted by publication date asc or desc, eg GET *api/v1/posts?order=desc*

**published:** boolean. Fetches only the posts that have a publication date (aka are published), eg *GET api/v1/posts?published=true*

**tag:** String. Fetches the posts that have a given tag/tags, GET api/v1/posts?tag=sports or GET *api/v1/posts?tag=sports,social*

## **POST api/v1/posts:** Create a new blog post
**title:** String, required.

**body:** String, required (accepts html tags)

**tags:** Array, required

**author_id:** Integer, required, a valid user id

**published_at:** Optional, date, format: 'Y-m-d H:i:s', should be before or equal to now.

Sample POST request body:
```json
{
    "title": "Lorem Ipsum",
    "body": "Magni fuga veniam explicabo ex rerum dolorum voluptatibus. Officiis quia labore atque natus at. Et sed maiores consequuntur perferendis maxime mollitia odit.",
    "tags": [
        "sport",
        "social"
    ],
    "author_id": 1,
    "published_at": "2012-03-12 07:14:26"
}
```
Sample response:
```json
{
    "title": "Lorem Ipsum",
    "body": "Magni fuga veniam explicabo ex rerum dolorum voluptatibus. Officiis quia labore atque natus at. Et sed maiores consequuntur perferendis maxime mollitia odit.",
    "tags": [
        "sport",
        "social"
    ],
    "published_at": "2012-03-12 07:14:26",
    "author_id": 1,
    "updated_at": "2018-01-16 13:37:09",
    "created_at": "2018-01-16 13:37:09",
    "id": 71
}
```

## **PUT api/v1/posts/{id}:** Update an existing blog post
**title:** String, required.

**body:** String, required (accepts html tags)

**tags:** Array, required

**author_id:** Integer, required, a valid user id

**published_at:** Optional, date, format: 'Y-m-d H:i:s', should be before or equal to now.

Sample PUT request body:
```json
{
    "title": "Lorem Ipsum",
    "body": "Magni fuga veniam explicabo ex rerum dolorum voluptatibus. Officiis quia labore atque natus at. Et sed maiores consequuntur perferendis maxime mollitia odit.",
    "tags": [
        "sport",
        "social"
    ],
    "author_id": 1,
    "published_at": "2012-03-12 07:14:26"
}
```

## **DELETE api/v1/posts/{id}:** Delete an existing blog post

# Additional Info
- POST requests may be a bit slow, as an event is fired in order to send an info email to admin. The event uses queues so in production envs
this would be no issue, since the QUEUE_DRIVER would be anything but sync.
- All string inputs are sanitized, especially the _body_ property (which allows html tags) is sanitized using HTML_Purifier.
- In compliance with KISS, tags are not kept in a separate table using many-to-many relationship but instead saved as comma-separated
strings with a start and trailing comma (kind of hack to facilate DB queries using LIKE). Example: ['sports', 'social'] would be
stored as: ',sports,social,'. This is accomplished using Laravel's accessors and mutators.

# Unit Tests
Run unit tests with:
```docker-compose run app vendor/bin/phpunit```
