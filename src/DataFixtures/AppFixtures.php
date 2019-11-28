<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\City;
use App\Entity\TagCustom;
use App\Entity\User;
use App\Utils\Slugger;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadTags($manager);
        $this->loadPosts($manager);
        $this->loadCities($manager);
        $this->loadTagCustom($manager);
    }

    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$fullname, $username, $password, $email, $roles]) {
            $user = new User();
            $user->setFullName($fullname);
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($username, $user);
        }

        $manager->flush();
    }

    private function loadTags(ObjectManager $manager): void
    {
        foreach ($this->getTagData() as $index => $name) {
            $tag = new Tag();
            $tag->setName($name);

            $manager->persist($tag);
            $this->addReference('tag-'.$name, $tag);
        }

        $manager->flush();
    }

    private function loadCities(ObjectManager $manager): void
    {
        foreach ($this->getCityData() as $index => $name) {
            $city = new City();
            $city->setName($name);

            $manager->persist($city);
            $this->addReference('city-' . $name, $city);
        }

        $manager->flush();
    }

    private function loadTagCustom(ObjectManager $manager): void
    {
        foreach ($this->getTagCustomData() as $index => $name) {
            $tagsCustom = new TagCustom();
            $tagsCustom->setName($name);

            $manager->persist($tagsCustom);
            $this->addReference('tagcustom-' . $name, $tagsCustom);
        }
        $manager->flush();
    }

    private function loadPosts(ObjectManager $manager): void
    {
        foreach ($this->getPostData() as [$title, $slug, $summary, $content, $publishedAt, $author, $tags]) {
            $post = new Post();
            $post->setTitle($title);
            $post->setSlug($slug);
            $post->setSummary($summary);
            $post->setContent($content);
            $post->setPublishedAt($publishedAt);
            $post->setAuthor($author);
            $post->addTag(...$tags);

            foreach (range(1, 5) as $i) {
                $comment = new Comment();
                $comment->setAuthor($this->getReference('john_user'));
                $comment->setContent($this->getRandomText(random_int(255, 512)));
                $comment->setPublishedAt(new \DateTime('now + '.$i.'seconds'));

                $post->addComment($comment);
            }

            $manager->persist($post);
        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            // $userData = [$fullname, $username, $password, $email, $roles];
            ['Jane Doe', 'jane_admin', 'kitten', 'jane_admin@symfony.com', ['ROLE_ADMIN']],
            ['Samuel', 'samuel', 'samuel', 'etheve.samuel@gmail.com', ['ROLE_ADMIN']],
            ['John Doe', 'john_user', 'kitten', 'john_user@symfony.com', ['ROLE_USER']],
        ];
    }

    private function getTagData(): array
    {
        return [
            'lorem',
            'ipsum',
            'consectetur',
            'adipiscing',
            'incididunt',
            'labore',
            'voluptate',
            'dolore',
            'pariatur',
        ];
    }

    private function getCityData(): array
    {
        return [
            'Salazie 97433',
            'Sainte-Suzanne 97441',
            'Sainte-Rose 97439',
            'Sainte-Marie 97438',
            'Saint-Pierre 97410',
            'Saint-Philippe 97442',
            'Saint-Paul 97460',
            'Saint-Louis 97450',
            'Saint-Leu 97436',
            'Saint-Joseph 97480',
            'Saint-Denis 97400',
            'Saint-Benoit 97470',
            'Saint-André 97440',
            'Petite-Île 97429',
            'Le Tampon 97430',
            'Cilaos 97413',
            'La Possession 97419',
            'Mafate 97419',
            'La plaine des palmistes 97431',
            'La Nouvelle 97428',
            'Etang-Salé 97427',
            'L\'Entre-Deux 97414',
        ];
    }

    private function getTagCustomData(): array
    {
        return [
        "Accommodations",
        "Leisure activities",
        "Craftsmanship",
        "Restoration",
        "Transportation",
        "Guide",
        "Heritage",
        "Services",
        "Others",
        ];
    }

    private function getPostData()
    {
        $posts = [];
        foreach ($this->getPhrases() as $i => $title) {
            // $postData = [$title, $slug, $summary, $content, $publishedAt, $author, $tags, $comments];
            $posts[] = [
                $title,
                Slugger::slugify($title),
                $this->getRandomText(),
                $this->getPostContent(),
                new \DateTime('now - '.$i.'days'),
                // Ensure that the first post is written by Jane Doe to simplify tests
                $this->getReference(['jane_admin', 'samuel'][0 === $i ? 0 : random_int(0, 1)]),
                $this->getRandomTags(),
            ];
        }

        return $posts;
    }

    private function getPhrases(): array
    {
        return [
            'Lorem ipsum dolor sit amet consectetur adipiscing elit',
            'Pellentesque vitae velit ex',
            'Mauris dapibus risus quis suscipit vulputate',
            'Eros diam egestas libero eu vulputate risus',
            'In hac habitasse platea dictumst',
            'Morbi tempus commodo mattis',
            'Ut suscipit posuere justo at vulputate',
            'Ut eleifend mauris et risus ultrices egestas',
            'Aliquam sodales odio id eleifend tristique',
            'Urna nisl sollicitudin id varius orci quam id turpis',
            'Nulla porta lobortis ligula vel egestas',
            'Curabitur aliquam euismod dolor non ornare',
            'Sed varius a risus eget aliquam',
            'Nunc viverra elit ac laoreet suscipit',
            'Pellentesque et sapien pulvinar consectetur',
            'Ubi est barbatus nix',
            'Abnobas sunt hilotaes de placidus vita',
            'Ubi est audax amicitia',
            'Eposs sunt solems de superbus fortis',
            'Vae humani generis',
            'Diatrias tolerare tanquam noster caesium',
            'Teres talis saepe tractare de camerarius flavum sensorem',
            'Silva de secundus galatae demitto quadra',
            'Sunt accentores vitare salvus flavum parses',
            'Potus sensim ad ferox abnoba',
            'Sunt seculaes transferre talis camerarius fluctuies',
            'Era brevis ratione est',
            'Sunt torquises imitari velox mirabilis medicinaes',
            'Mineralis persuadere omnes finises desiderium',
            'Bassus fatalis classiss virtualiter transferre de flavum',
        ];
    }

    private function getRandomText(int $maxLength = 255): string
    {
        $phrases = $this->getPhrases();
        shuffle($phrases);

        while (mb_strlen($text = implode('. ', $phrases).'.') > $maxLength) {
            array_pop($phrases);
        }

        return $text;
    }

    private function getPostContent(): string
    {
        return <<<'MARKDOWN'
        Lorem ipsum dolor sit amet consectetur adipisicing elit, sed do eiusmod tempor
        incididunt ut labore et **dolore magna aliqua**: Duis aute irure dolor in
        reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
        Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
        deserunt mollit anim id est laborum.

        * Ut enim ad minim veniam
        * Quis nostrud exercitation *ullamco laboris*
        * Nisi ut aliquip ex ea commodo consequat

        Praesent id fermentum lorem. Ut est lorem, fringilla at accumsan nec, euismod at
        nunc. Aenean mattis sollicitudin mattis. Nullam pulvinar vestibulum bibendum.
        Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos
        himenaeos. Fusce nulla purus, gravida ac interdum ut, blandit eget ex. Duis a
        luctus dolor.

        Integer auctor massa maximus nulla scelerisque accumsan. *Aliquam ac malesuada*
        ex. Pellentesque tortor magna, vulputate eu vulputate ut, venenatis ac lectus.
        Praesent ut lacinia sem. Mauris a lectus eget felis mollis feugiat. Quisque
        efficitur, mi ut semper pulvinar, urna urna blandit massa, eget tincidunt augue
        nulla vitae est.

        Ut posuere aliquet tincidunt. Aliquam erat volutpat. **Class aptent taciti**
        sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi
        arcu orci, gravida eget aliquam eu, suscipit et ante. Morbi vulputate metus vel
        ipsum finibus, ut dapibus massa feugiat. Vestibulum vel lobortis libero. Sed
        tincidunt tellus et viverra scelerisque. Pellentesque tincidunt cursus felis.
        Sed in egestas erat.

        Aliquam pulvinar interdum massa, vel ullamcorper ante consectetur eu. Vestibulum
        lacinia ac enim vel placerat. Integer pulvinar magna nec dui malesuada, nec
        congue nisl dictum. Donec mollis nisl tortor, at congue erat consequat a. Nam
        tempus elit porta, blandit elit vel, viverra lorem. Sed sit amet tellus
        tincidunt, faucibus nisl in, aliquet libero.
        MARKDOWN;
    }

    private function getRandomTags(): array
    {
        $tagNames = $this->getTagData();
        shuffle($tagNames);
        $selectedTags = \array_slice($tagNames, 0, random_int(2, 4));

        return array_map(
            function ($tagName) {
                return $this->getReference('tag-'.$tagName); 
            }, $selectedTags);
    }
}
