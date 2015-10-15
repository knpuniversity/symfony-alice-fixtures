# Processors: Do Custom Stuff While Loading

***TIP
A newer version of HauteLookAliceBundle has been released and portions of this
tutorial won't apply to that new version.
***

I don't want to brag, but these are probably the nicest super-hero fixtures
ever. But we've neglected a column! The avatar.

Check out the `Character` entity - we have a column for this called `avatarFilename`:

[[[ code('3d91d9bc8b') ]]]

It's going to hold *just* the filename part of an image, like `trogdor.png`
or `pac-man-is-the-man.jpg`. This makes sense if you look in the template
for the homepage. *If* there's an `avatarFilename`, we print an `img` tag
and expect the image to be in some `uploads/avatars` directory, relative
to `web/`:

[[[ code('5c2cd45ec5') ]]]

Oh boy, so this means the avatar is a bit harder. Yea, we have to set the
value in the database, but we *also* need to make sure to put a corresponding
image file into this directory. I don't want a bunch of broken images!

## Filling in avatarFilename Data

But, we'll worry about that later. First, let's get some values into the
`avatarFilename` field. Open up `characters.yml` and start to set the `avatarFilename`.

[[[ code('807df17ca4') ]]]

For *any* of this to work, we're going to need some *real* image files handy.
Fortunately, I got some for us! They live in a `resources` directory at the
root of the project:

```
resources/
    kitten1.jpg
    kitten2.jpg
    kitten3.jpg
    kitten4.jpg
```

But since I want to avoid any trademark legal battles with Nintendo, I've
decided that instead of Mario and Yoshi, we'll use readily-available images
of kittens. Thank you Internet.

So we need our value to be *one* of these. Let's setup a custom Faker formatter
like we did before. Call this one `avatar()`:

[[[ code('fce18a666d') ]]]

Try reloading the fixtures now:

```
php app/console doctrine:fixtures:load
```

Ah, there's our error!

    Unknown formatter "avatar"

Time to fix that! Open `AppFixtures` and create a new public function called
`avatar()`. To keep things lazy, let's copy the guts of `characterName()`
and update the options to be `kitten1.jpg`, then 2, 3 and 4. Sweet!

[[[ code('146f89e53f') ]]]

Reload reload! ... the fixtures:

```
php app/console doctrine:fixtures:load
```

Great, and now reload our page. Ah, broken images! Yay! The `img` tags are
printing out beautifully, but there isn't *actually* a `kitten3.jpg` file
inside the `uploads/avatars` directory. We've got work to do!

## Creating the Processor

This is where Processors come in. Whenever you need to do something *other*
than just setting simple data, you'll use a Processor, which is like a hook
that's called before and after each object is saved.

Step1! Create a new class. It doesn't matter where it goes, so put it inside
`ORM/` and call it `AvatarProcessor`. The only rule of a processor is that
it needs to implement `ProcessorInterface`. And that means we have to have
two methods: `postProcess()` and `preProcess()`.

Each is passed whatever object is being saved right now, so let's just dump
the class of the object:

[[[ code('28d4848632') ]]]

Cool new processor class, check! To hook it up, go back into `AppFixtures`.
The parent `DataFixturesLoader` class has an empty `getProcessors()` method
that we need to override. Because it's empty, we don't need to call the parent.
Just return an array with a new `AvatarProcessor` object in it:

[[[ code('a2277b42d1') ]]]

Let's reload the fixtures to see what happens!

```
php app/console doctrine:fixtures:load
```

Cool! It calls `preProcessor` for *every* object - whether it's a `Universe`
or a `Character`.

## Moving Images Around

Ok, let's copy some images. First, we only want to do work if the object
that's passed to us is a `Character`. So, if we're *not* an instance of
`Character`, just return:

[[[ code('4b00ebe183') ]]]

Next, some Character's don't have an avatar, so if this doesn't have an
`avatarFilename`, we'll just return - we don't need to move any files around:

[[[ code('f74b051f97') ]]]

Now we *know* there's an `avatarFilename`. We also know that the originals
live in this `resources/` directory, so we just need to copy those into the
`web/uploads/avatars` directory.

First, create a variable that points to the root directory of our project.
This will get me all the way back to the root - there are other ways to do
this, but this is simple.

To do the copying, let's use Symfony's `Filesystem` object - it does nice
things like create the directory if it doesn't exist. And hey, that's nice!
My editor just added the `use` statement for me. Now, call `copy()`. The
original file is `$projectRoot`, `resources`, then the `avatarFilename`.
The destination is `$projectRoot` again, then to `web/uploads/avatars` then
the object's `avatarFilename`:

[[[ code('63019f03ab') ]]]

We're using this directory because that's what my app is expecting in the
template. The third argument is whether to override an existing file. And
that should get the job done! Reload those fixtures!

```
php app/console doctrine:fixtures:load
```

Now refresh! Ok, super-hero kittens! And if you want to know how to get
access to the container in a Processor, keep watching.
