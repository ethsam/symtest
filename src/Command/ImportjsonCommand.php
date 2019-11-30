<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\PostCustom;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ImportjsonCommand extends Command
{
    protected static $defaultName = 'app:importjson';

    /**
     * @var EntityManagerInterface $manager
     */
    private $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('File for import : %s', $arg1));

            $file = file_get_contents($arg1);
            $json = json_decode($file, true);

            //$author = new User;
            $count = 0;

            $user = $this->manager->getRepository("App\Entity\User");
            $city = $this->manager->getRepository("App\Entity\City");
            $tagCustomRep = $this->manager->getRepository("App\Entity\TagCustom");

            //$io->note($city->findAll());

            foreach ($json as $key => $val) {
                $post = new PostCustom();

                $idCity = $val['listing_location'];
                $idTagCustom = $val['listing_category'];

                $post->setTitle($val["post_title"]);
                $post->setSlug($val["post_name"]);
                $post->setSummary("test");
                $post->setContent($val["post_content"]);
                $post->setImgname($val["featured_image"]);
                $post->setImgalt($val["post_title"]);
                $post->setLat($val["lv_listing_lat"]);
                $post->setLng($val["lv_listing_lng"]);
                $post->setPublishedAt(new \DateTime('now + ' . $key . 'seconds'));
                $post->setAuthor($user->find(2));
                $post->addCity($city->find($idCity));
                $post->addTag($tagCustomRep->find($idTagCustom));

                $count = $count + 1;

                $this->manager->persist($post);
            }

            $this->manager->flush();

            $io->note($count." Elements imported");
           
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('Finish');

        return 0;
    }
}
