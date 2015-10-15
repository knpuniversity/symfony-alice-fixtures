# Creating Unique Files

***TIP
A newer version of HauteLookAliceBundle has been released and portions of this
tutorial won't apply to that new version.
***

There's a little issue. These two kittens are the *exact* same filename.
The first is `kitten2.jpg` and so is the second. That's fine for us, but
imagine if we could delete characters, and if doing that deleted the image.
If we deleted *this* character, it would delete the image for the second one
too. To be more realistic, each character needs a unique image.

NO problem. Setup a new `$targetFilename` instead of using the original filename.
Set it to `fixtures_` then `mt_rand()` and `.jpg`:

[[[ code('8f039985c1') ]]]

Copy the file to *this* filename. And make sure that the `avatarFilename`
is our new, random thing:

[[[ code('9d9e1601a6') ]]]

Time to reload those fixtures:

```
php app/console doctrine:fixtures:load
```

In `web/uploads/avatars`, we see a bunch of random filenames. And when we
refresh, they're all using different filenames. 

## Accessing the container in a Processor

You can do whatever you want inside a Processor, but with a glaring limitation
so far: you don't have access to the container or any of your services.

Let's try to log the random filenames being used for each `Character`. That
means we'll need the `logger` service, and right now we don't have access
to anything. To get it, we'll treat `AvatarProcessor` like any other service
and use dependency injection. Create a `__construct()` function, and type-hint
the argument with `LoggerInterface` from PSR. That'll add my `use` statement.
Now, set that on a `logger` property:

[[[ code('0e8b57c726') ]]]

Before worrying about how we'll pass in the logger, go down below and log
a debug message. Fill in the placeholders with the object's name, the `$targetFilename`
and then the original `avatarFilename`:

[[[ code('655d5ccd4a') ]]]

This class is *not* registered as a service - we just create it manually
in `AppFixtures`:

[[[ code('a2277b42d1') ]]]

Passing the logger in is simple. The base `DataFixturesLoader` class *has*
the container and puts it on a `$container` property, just like a Controller.
So we can say `$this->container->get('logger')`:

[[[ code('08192b2efd') ]]]

To test this out, open up a new tab and let's tail the `app/logs/dev.log`
directory, because `app/console` runs in the `dev` environment by default.
And let's grep it for the word `Character`:

```
tail -f app/logs/dev.log | grep "Character"
```

Now reload the fixtures!

```
php app/console doctrine:fixtures:load
```

No errors, AND we get our log messages. Btw, you can also see log messages
directly when running a command by passing the `-vvv` option:

```
php app/console doctrine:fixtures:load -vvv
```

This can be pretty handy. 

This means that there's *nothing* you can't do with a Processor. Need a service?
Just use normal dependency injection, pass it in, do awesome things with
your fixtures, then celebrate.

Cheers!
