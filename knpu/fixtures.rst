Hey guys! Fixtures are those little bits of data that you can load when you're 
building something so that you actually have some data on your screen. Like 
products or users or whatever else you want to play with. I hate writing 
fixtures because I use this bundle and it's boring and manual and you end up 
building really big ugly fixture classes. The reason is that this library just 
doesn't do anything for you. It's entirely up to you to manage, persist and 
flush everything that you do. So I'm going to show you a better way in fact a 
much better way with a library called Alice. I prepared a small Symfony 
application for us which we'll talk about in a second. And I've already started 
our built-in PHP Web server. And here's our app, it's all about listing our 
favorite videogame heros. Right now we're losing because there is nothing in 
this table but that's what we're going to fix with Alice. This table is actually 
pulling from the character entity so this is what we actually need to save 
to the database. To install Alice we could do it directly but instead I'm going
to use an awesome bundle called HauteLook/AliceBundle. Let's grab the composer 
require command from it's readme and paste that into the terminal. This bundle 
is a thin layer around the Alice library which is something that let's us load 
fixtures with yml files and the same doctrine fixtures bundle that we're talking 
about before. This is a really nice combination because it's going to mean that 
we can still run our normal `./app/console doctrine:fixtures:load` but after that 
instead of writing raw PHP code all of our fixtures are going to be in these 
really nice yml files.
  
   
And if that doesn't sound awesome yet, just hang with me. Alice is a lot more 
than yml files it contains tons of goodies. Next let's activate the bundle. In 
fact if you head back to its documentation you'll see that you need to 
initialize both this bundle and the doctrine fixtures bundle in our app bundle. 
So grab both of those lines, open the app bundle and let's put it there. But 
since I'm only going to be loading my fixtures when I'm developing I'm going to 
go ahead and put these inside of the dev environment block. That way in 
production I have just a little bit less in my application. You do need one 
fixture class but we can just copy it from the documentation and put it into 
our application. I'll create the `DataFixtures/ORM' directory. By the way this 
stuff does work with the ODM or other doctrine libraries. And I'll create a 
file called `AppFixtures`. Copy the contents in there and don't forget to update 
your namespace and rename the class. The fixtures class is special because it's 
already wired up to load yml files. Let's call ours `characters.yml` and then go 
ahead and create that file. Now, here is how Alice works. inside the yml file 
this is now pure Alice code. You start with the full entity namespace. This 
tells Alice what type of object it's going to create. Below that we just start 
inventing keys. These aren't important yet but they will be later when we start 
linking two entities together. Under that we just give each property a value.
Let's create Yoshi. Let's cheat and look back at the character entity to see 
what other fields we want to fill in. We now have a fully functional and armed 
single line fixture. So let's try it out. 

As I mentioned earlier this is a wrapper around the doctrine fixtures 
library so we use the same `./app/console doctrine:fixtures:load` command to 
run everything. No errors is good so let's try refreshing the page. Yoshi! 
If this is all that Alice gave us I wouldn't be telling you about it. It 
actually gives us a ton more. So usually in fixtures you want a lot of things. Like 
five characters or ten characters or 50 blog posts or something like that. 

One of the most powerful features of Alice is this range syntax. So, in this 
case we're going to be creating characters two through 10. Behind the scenes you 
can see how this is basically a for loop but the syntax is a lot cleaner. To 
test that out let's reload our fixtures, and now Mario is taking over our 
database. So we have 10 characters now but since nine of them are identical 
they're not very realistic. But this is where Alice gets really interesting. 
It has this special <> syntax which allows you to call functions that are 
special to Alice. For example, when you're inside of a range you can use this 
syntax to call the current function that's going to give us whatever index were at that 
moment. So let's reload our fixtures again and now we have Mario2, Mario3, 
Mario4. So this is better but still not very realistic. Behind the scences Alice
hooks up with another library called Faker. And as it's name sounds it's all 
about creating fake data. Fake names, fake company names, fake addresses, fake 
e-mails it supports a ton of stuff. To use Faker we just use that same syntax we 
saw and use one of the many built-in functions. For example, one of the functions 
is called `firstName` since this is going to return us some pretty normal names 
let's put the word super in front of it so at least it sounds like a superhero. 
Then we're going to use a few others like `name`, `numberBetween`, `email` and 
`sentence` which gives us one random sentence. These functions are pretty 
self-explanatory but if you Google for fakerPHP and scroll down on the readme 
just a little bit. They have a huge list of all the functions that they support. 
They're actually called formatters but a lot of them take arguments. For example 
you can see our `numberBetween`, `sentence` and even some things for creating 
random names where you can choose which gender you want. So let's check this out. 
Reload your fixtures, scroll back over refresh the page. Now we have ten super 
friends and no identical data. If you want to make one of these fields sometimes 
empty you can do that as well. For example, if tagline is optional then you may want to see 
what your set looks like when some of the characters don't have one. To do that
create a percentage put a ? after it and then list what value you want. So in this
case 80% of the time we're going to get a random sentence and 20% of the time we're
going to get nothing.

So reload the fixtures, and this time you see that about 20% of our characters
are missing their tag line. So I love the random data, I love how easy this is.
But one thing I don't like is that our names just aren't that realistic. We're
dealing with video game heros here and none of our names are actually of real
video game heros. To fix this let's create our own formatter called 
`characterName`. Now if you try this out you are going to get the error that the
formatter is  missing. So how do we create it? With the bundle it's super easy.
Just go back to your fixtures class, `appFixtures` and create a function called
`charaterName`. And in this function we just need to return a character name.
I'll paste in a few of my favorites and then at the bottom we'll use the 
`array_rand` function to return a random character each time Alice calls this.

I love when things are this simple. Flip back to the browser and when you refresh
this time, real video game heros! So there's one more complication that I want
to introduce, and that's relationships. I have an entity called `Universe`as in 
Nintendo Universe or Sega Universe. First, let's go into our yml file and create
a few of these. We'll start just like before by putting the namespace and creating
a few entries under that. So I'll have one for Nintendo, one for Sega and one
for classic arcade. 

The character entity already has a Many to One relationship to universe on a 
universe property. So our goal is to take these universe objects and set them 
on the charcter. 

To reference another object just use the @ symbol and then the
internal key to that object. So we'll link Mario to the Nintendo universe and 
everyone else, for now, to the Sega Universe. When we check it out now sure enough
we see Nintendo on top followed by 9 Segas. So I know you're thinking, "can we somehow
randomly assign random universes to the characters?" and absolutely! In fact,
the syntax is ridiculously straight forward. Just get rid of the sega part and put a star.
Now, Alice is going to find any keys that start with `universe_` and randomly assign
them to the characters. Reload things again and now we have a nice assortment
of universes. Because our project is pretty small I've kept everyhthing in a single
file which I recommend that you do until it gets just too big. Once it does
feel free to separate into multiple yml files. In our case I'll create a `universe.yml`
file and put the universe stuff in it. Of course when you do this it's not going
to work because it's only loading the characters.yml file right now. So we get
a missing reference error. There are actually a few ways to load the two yml files
but the easiest is to go back into your `appFixtures` class and just add it to the
array. Unfortunately, order is important here. So since we're referencing the universes
from within the `characters.yml` we need to load the `universe.yml` file first. 
Let's reload things to make sure they're working and they are! 

To back up after we installed the bundle we only really touched two things. 
The `appFixtures` class, which has almost nothing in it. And our yml files which
are very very small and straight forward. This is awesome! This puts the joy
back into writing fixtures files for me and I absolutely love it. There are a few
topics that we haven't talked about like providers and template but I'll try
to cover those in a future lesson. 

See you guys!