<?php

namespace AppBundle\Command;

use AppBundle\Entity\Movie;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetApiMoviesCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:get_movies')
            ->setDescription('Command to get countries');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);

        $json = file_get_contents('http://localhost:8000/movies');
        $movies = json_decode($json, true);
//        $backtoJson = serialize($movies);
//        dump($backtoJson); die;

        $i = 0;
        foreach ($movies as $movie) {
            $movieObject = (new Movie())
                ->setTitle($movie['title'])
                ->setYear($movie['year'])
                ->setTime($movie['time'])
                ->setDescription($movie['description']);
            dump($movieObject);
            $i++;
        }

        $style->success(sprintf('Znaleziono %d filmów w bazie danych!', $i));
    }
    //php bin/console app:get_movies
}
