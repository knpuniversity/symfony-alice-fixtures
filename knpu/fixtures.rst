Making Fixtures Awesome with Alice
==================================

.. tip::

    A newer version of HauteLookAliceBundle has been released and portions
    of this tutorial won't apply to that new version.

Fixtures, those nice little bits of data that you can put into your database
so that when you're developing locally you actually have something to look at:
like products or users or whatever else you want to play with. I hate writing
fixtures because I use this bundle (`DoctrineFixturesBundle`_) and it's boring
and manual and you end up building really big ugly fixture classes::

    // a boring DataFixtures class
    // ...

    class MyFixtures implements FixtureInterface
    {
        public function load(ObjectManager $manager)
        {
            $user1 = new User();
            $user1->setUsername('foo');
            // ...
            $manager->persist($user1);
            // repeat over and over...

            $manager->flush();
        }
    }

The reason is that this library just doesn't do anything for you. It's entirely
up to you to manage, persist and flush everything that you do.

Introducing Alice + Our Video Game Hero App
-------------------------------------------

So I'm going to show you a better way, in fact a much better way with a library
called `Alice`_. I prepared a small Symfony application for us which we'll
talk about in a second. And I've already started our built-in PHP Web server:

.. tip::

    Want to code along? Beautiful! Just download the code on this page.

.. code-block:: bash

    php app/console server:run

And here's our app, it's all about listing our favorite videogame heroes.
Right now we're losing because there is nothing in this table. But that's
what we're going to fix with Alice.

This table is actually pulling from the ``Character`` entity so this is what
we actually need to save to the database.

Installing Alice via HauteLookAliceBundle
-----------------------------------------

To install Alice we could do it directly but instead I'm going to use an
awesome bundle called `HautelookAliceBundle`_. Let's grab the ``composer require``
command from it's README and paste that into the terminal:

.. code-block:: bash

    composer require hautelook/alice-bundle

This bundle is a thin layer around the Alice library, which is something that
let's us load fixtures with yml files, and the same DoctrineFixturesBundle
that we were talking about before. This is a really nice combination because
it's going to mean that we can still run our normal ``php app/console doctrine:fixtures:load``.
But after that, instead of writing raw PHP code, all of our fixtures are
going to be in these really nice yml files.

And if that doesn't sound awesome yet, just hang with me. Alice is a lot more 
than yml files - it contains tons of goodies.

Next let's activate the bundle. In fact if you head back to its documentation
you'll see that you need to initialize both this bundle *and* the ``DoctrineFixturesBundle``
in our ``AppKernel``. So grab both of those lines, open the ``AppKernel``
and let's put it there::

    // app/AppKernel.php
    // ...
    
    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        // ...

        $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        $bundles[] = new Hautelook\AliceBundle\HautelookAliceBundle();
    }

But since I'm only going to be loading my fixtures when I'm developing, I'm
going to go ahead and put these inside of the ``dev`` environment block.
That way, in production I have just a little bit less in my application.

You *do* need one fixture class, but we can just copy it from the documentation
and put it into our application. I'll create the ``DataFixtures/ORM`` directory.
By the way this stuff *does* work with the ODM or other doctrine libraries.
And I'll create a file called ``AppFixtures``. Copy the contents in there
and don't forget to update your namespace and rename the class::

    // src/AppBundle/DataFixtures/ORM/AppFixtures.php
    namespace AppBundle\DataFixtures\ORM;

    use Hautelook\AliceBundle\Alice\DataFixtureLoader;
    use Nelmio\Alice\Fixtures;

    class AppFixtures extends DataFixtureLoader
    {
        /**
         * {@inheritDoc}
         */
        protected function getFixtures()
        {
            return  array(
                __DIR__ . '/test.yml',
            );
        }
    }

The fixtures class is special because it's already wired up to load yml files.
Let's call ours ``characters.yml`` and then go ahead and create that file::

    // src/AppBundle/DataFixtures/ORM/AppFixtures.php
    // ...    

    protected function getFixtures()
    {
        return  array(
            __DIR__ . '/characters.yml',
        );
    }

Your First Alice yml File
-------------------------

Now, here is how Alice works. Inside the yml file this is now pure Alice
code. You start with the full entity namespace. This tells Alice what type
of object it's going to create. Below that, we just start inventing keys.
These aren't important yet but they *will* be later when we start linking
two entities together. Under that we just give each property a value. Let's
create Yoshi:

.. code-block:: yaml

    # src/AppBundle/DataFixtures/ORM/characters.yml
    AppBundle\Entity\Character:
        character1:
            name: Yoshi
            realName: T. Yoshisaur Munchakoopas
            highScore: 99999
            email: yoshi@nintendo.com
            tagLine: Yoshi!

Let's cheat and look back at the ``Character`` entity to see what other fields
we want to fill in. We now have a fully functional and armed single-file
fixture. So let's try it out. 

Loading your Fixtures
---------------------

As I mentioned earlier, this is a wrapper around the Doctrine fixtures library
so we use the same ``php app/console doctrine:fixtures:load`` command to 
run everything. No errors is good so let's try refreshing the page. Yoshi! 

Loading A LOT of Test Data (Ranges)
-----------------------------------

If this is all that Alice gave us I wouldn't be telling you about it. It 
actually gives us a ton more. So usually in fixtures you want a lot of things.
Like five characters or ten characters or 50 blog posts or something like that.

One of the most powerful features of Alice is this range syntax:

.. code-block:: yaml

    # src/AppBundle/DataFixtures/ORM/characters.yml
    AppBundle\Entity\Character:
        character1:
            name: Yoshi
            realName: T. Yoshisaur Munchakoopas
            highScore: 99999
            email: yoshi@nintendo.com
            tagLine: Yoshi!
        character{2..10}:
            name: Mario
            realName: Homo Nintendonus
            highScore: 50000
            email: mario@nintendo.com
            tagLine: Let's a go!

So, in this case we're going to be creating characters two through 10. Behind
the scenes you can see how this is basically a ``for`` loop but the syntax
is a lot cleaner. To test that out let's reload our fixtures:

.. code-block:: bash

    php app/console doctrine:fixtures:load

And now Mario is taking over our database!

So we have 10 characters now but since nine of them are identical they're
not very realistic. But this is where Alice gets really interesting. It has
this special ``<>`` syntax which allows you to call functions that are special
to Alice.

For example, when you're inside of a range you can use this syntax to call
the ``<current()>`` function that's going to give us whatever index were at
in that moment:

.. code-block:: yaml

    # src/AppBundle/DataFixtures/ORM/characters.yml
    AppBundle\Entity\Character:
        # ...
        character{2..10}:
            name: Mario<current()>
            realName: Homo Nintendonus
            # ...

So let's reload our fixtures again and now we have Mario2, Mario3, Mario4.

Introducing Faker: For all your Fake Data Needs
-----------------------------------------------

So this is better but still not very realistic. Behind the scences Alice
hooks up with another library called `Faker`_. And as it's name sounds it's
all about creating fake data. Fake names, fake company names, fake addresses,
fake e-mails - it supports a ton of stuff. To use Faker we just use that same
syntax we saw and use one of the many built-in functions.

For example, one of the functions is called ``firstName()``. Since this is
going to return us some pretty normal names, let's put the word ``Super``
in front of it so at least it sounds like a superhero:

.. code-block:: yaml

    # src/AppBundle/DataFixtures/ORM/characters.yml
    AppBundle\Entity\Character:
        # ...
        character{2..10}:
            name: Super <firstName()>
            realName: Homo Nintendonus
            # ...

Then we're going to use a few others like ``name()``, ``numberBetween()``,
``email()`` and ``sentence`` which gives us one random sentence:

.. code-block:: yaml

    # src/AppBundle/DataFixtures/ORM/characters.yml
    AppBundle\Entity\Character:
        # ...
        character{2..10}:
            name: Super <firstName()>
            realName: <name()>
            highScore: <numberBetween(100, 99999)>
            email: <email()>
            tagLine: <sentence()>

These functions are pretty self-explanatory but if you Google for "Faker PHP"
and scroll down on the README just a little bit, they have a `huge list`_
of all the functions that they support. They're actually called formatters
but a lot of them take arguments.

For example you can see our ``numberBetween``, ``sentence`` and even some
things for creating random names where you can choose which gender you want.
So let's check this out. Reload your fixtures, scroll back over refresh the page.

.. code-block:: bash

    php app/console doctrine:fixtures:load

Now we have ten super friends and no identical data.

Making a Field (sometimes) Blank
--------------------------------

If you want to make one of these fields sometimes empty you can do that as
well. For example, if ``tagLine`` is optional then you may want to see what
your set looks like when some of the characters don't have one. To do that
create a percentage put a ? after it and then list what value you want:

.. code-block:: yaml

    # src/AppBundle/DataFixtures/ORM/characters.yml
    AppBundle\Entity\Character:
        # ...
        character{2..10}:
            # ...
            tagLine: 80%? <sentence()>

So in this case 80% of the time we're going to get a random sentence and 20%
of the time we're going to get nothing. So reload the fixtures, and this time
you see that about 20% of our characters are missing their tag line.

Creating your Own Faker Formatter (Function)
--------------------------------------------

So I love the random data, I love how easy this is. But one thing I don't
like is that our names just aren't that realistic. We're dealing with video
game heroes here and none of our names are actually of real video game heroes.

To fix this let's create our own formatter called ``characterName``:

.. code-block:: yaml

    # src/AppBundle/DataFixtures/ORM/characters.yml
    AppBundle\Entity\Character:
        # ...
        character{2..10}:
            name: <characterName()>
            realName: <name()>
            highScore: <numberBetween(100, 99999)>
            email: <email()>
            tagLine: <sentence()>

Now if you try this out you are going to get the error that the formatteris
missing:

    Unknown formatter "characterName"

So how do we create it? With the bundle it's super easy. Just go back to
your fixtures class, ``AppFixtures`` and create a function called ``characterName``.
And in this function we just need to return a character name. I'll paste
in a few of my favorites and then at the bottom we'll use the :phpfunction:`array_rand`
function to return a random character each time Alice calls this::

    // src/AppBundle/DataFixtures/ORM/AppFixtures.php
    // ...

    class AppFixtures extends DataFixtureLoader
    {
        // ...

        public function characterName()
        {
            $names = array(
                'Mario',
                'Luigi',
                'Sonic',
                'Pikachu',
                'Link',
                'Lara Croft',
                'Trogdor',
                'Pac-Man',
            );

            return $names[array_rand($names)];
        }
    }

I love when things are this simple!

.. code-block:: bash

    php app/console doctrine:fixtures:load

Flip back to the browser and when you refresh this time, real video game
heroes!

True Love with Relationships
----------------------------

So there's one more complication that I want to introduce, and that's relationships.
I have an entity called ``Universe`` as in "Nintendo Universe" or "Sega Universe".

First, let's go into our yml file and create a few of these. We'll start
just like before by putting the namespace and creating a few entries under
that. So I'll have one for Nintendo, one for Sega and one for classic arcade:

.. code-block:: yaml

    # src/AppBundle/DataFixtures/ORM/characters.yml
    AppBundle\Entity\Character:
        # ...

    AppBundle\Entity\Universe:
        universe_nintendo:
            name: Nintendo
        universe_sega:
            name: Sega
        universe_arcade:
            name: Classic Arcade

The ``Character`` entity already has a `ManyToOne`_ relationship to universe
on a ``universe`` property::

    // src/AppBundle/Entity/Character.php
    // ...
    
    class Character
    {
        // ...

        /**
         * @var Universe
         * @ORM\ManyToOne(targetEntity="Universe")
         */
        private $universe;
    }

So our goal is to take these ``Universe`` objects and set them on the ``charcter``
property. 

To reference another object, just use the ``@`` symbol and then the internal
key to that object. So we'll link Mario to the Nintendo universe and everyone
else, for now, to the Sega Universe:

.. code-block:: yaml

    # src/AppBundle/DataFixtures/ORM/characters.yml
    AppBundle\Entity\Character:
        character1:
            name: Yoshi
            # ...
            universe: @universe_nintendo

        character{2..10}:
            name: <characterName()>
            # ...
            universe: @universe_sega

    AppBundle\Entity\Universe:
        universe_nintendo:
            name: Nintendo
        universe_sega:
            name: Sega
        universe_arcade:
            name: Classic Arcade

.. code-block:: bash

    php app/console doctrine:fixtures:load

When we check it out now, sure enough we see Nintendo on top followed by
9 Segas. So I know you're thinking, "can we somehow randomly assign random
universes to the characters?" And absolutely! In fact, the syntax is ridiculously
straight forward. Just get rid of the ``sega`` part and put a star:

.. code-block:: yaml

    # src/AppBundle/DataFixtures/ORM/characters.yml
    AppBundle\Entity\Character:
        # ...

        character{2..10}:
            # ...
            universe: @universe_*

    AppBundle\Entity\Universe:
        universe_nintendo:
            name: Nintendo
        universe_sega:
            name: Sega
        universe_arcade:
            name: Classic Arcade

Now, Alice is going to find any keys that start with ``universe_`` and randomly
assign them to the characters. Reload things again and now we have a nice assortment
of universes:

.. code-block:: bash

    php app/console doctrine:fixtures:load

Using Multiple yml Files
------------------------

Because our project is pretty small I've kept everyhthing in a single file,
which I recommend that you do until it gets just too big. Once it does, feel
free to separate into multiple yml files.

In our case I'll create a ``universe.yml`` file and put the universe stuff
in it:

.. code-block:: yaml

    # src/AppBundle/DataFixtures/ORM/universe.yml
    # these have been removed from characters.yml
    AppBundle\Entity\Universe:
        universe_nintendo:
            name: Nintendo
        universe_sega:
            name: Sega
        universe_arcade:
            name: Classic Arcade

Of course when you do this it's not going to work because it's only loading
the characters.yml file right now. So we get a missing reference error:

    Reference universe_nintendo is not defined 

There are actually a few ways to load the two yml files but the easiest
is to go back into your ``AppFixtures`` class and just add it to the array::

    // src/AppBundle/DataFixtures/ORM/AppFixtures.php
    // ...

    protected function getFixtures()
    {
        return  array(
            __DIR__ . '/universe.yml',
            __DIR__ . '/characters.yml',
        );
    }

Unfortunately, order *is* important here. So since we're referencing the
universes from within the ``characters.yml`` we need to load the ``universe.yml``
file first. Let's reload things to make sure they're working.

.. code-block:: bash

    php app/console doctrine:fixtures:load

And they are! 

Joyful Fixtures
---------------

To back up, after we installed the bundle we only really touched two things. 
The ``AppFixtures`` class, which has almost nothing in it, and our yml files
which are very very small and straight forward. This is awesome! This puts
the joy back into writing fixtures files for me and I absolutely love it.

There are a few topics that we haven't talked about like processors and templates
but I'll cover those in a future lesson. 

See you guys!

.. _`DoctrineFixturesBundle`: http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
.. _`Alice`: https://github.com/nelmio/alice
.. _`HautelookAliceBundle`: https://github.com/hautelook/AliceBundle
.. _`Faker`: https://github.com/fzaninotto/Faker
.. _`huge list`: https://github.com/fzaninotto/Faker#formatters
.. _`ManyToOne`: http://knpuniversity.com/screencast/symfony2-ep3/doctrine-relationship
