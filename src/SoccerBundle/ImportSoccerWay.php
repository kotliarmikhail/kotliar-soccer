<?php

namespace SoccerBundle;

use Buzz;
use Symfony\Component\DomCrawler\Crawler;
use SoccerBundle\Entity\Team;
use SoccerBundle\Entity\Game;
use Doctrine\ORM\EntityManager;
use SoccerBundle\Manager\TeamManager;
use SoccerBundle\Manager\GameManager;

class ImportSoccerWay
{
    private $em;

    private $teamManager;

    private $gameManager;

    public function __construct(EntityManager $entityManager, TeamManager $teamManager, GameManager $gameManager)
    {
        $this->em = $entityManager;
        $this->teamManager = $teamManager;
        $this->gameManager = $gameManager;
    }

    public function fetchData()
    {
        $browser = new Buzz\Browser();
        $response = $browser->get('https://int.soccerway.com/national/england/premier-league/2011-2012/regular-season/r14829/matches/');
        $crawler = new Crawler($response->getContent());

        for ($tr = 0; $tr < $crawler->filter('tbody > tr')->filter('.team.team-a')->count(); $tr++) {

            //Date
            $timestamp = $this->getTimestampFromTable($crawler, $tr);

            $date = $this->getObjDateFromTimestamp('Y-m-d', $timestamp);

            //Home team
            $homeTeamName = $this->getTeamFromTable($crawler, $tr, 2);
            $homeTeam = $this->teamManager->getTeamByName($homeTeamName);

            // Score
            $score = $this->getScoreFromTable($crawler, $tr);
            $scoreArray = $this->getScoreArray($score);

            // Away Team
            $awayTeamName = $this->getTeamFromTable($crawler, $tr, 4);
            $awayTeam = $this->teamManager->getTeamByName($awayTeamName);

            $this->gameManager->setGameIfNotExist($date, $homeTeam, $awayTeam, $scoreArray);
        }
        return true;
    }

    private function getObjDateFromTimestamp($format, $timestamp)
    {
        $date = date($format, $timestamp);
        return new \DateTime($date);
    }

    private function getScoreArray($score)
    {
        $scoreArray = explode('-', $score);
        $homeTeamScore = trim($scoreArray['0']);
        $awayTeamScore = trim($scoreArray['1']);
        return array(
            'homeScore' => $homeTeamScore,
            'awayScore' => $awayTeamScore,
        );
    }

    private function getTimestampFromTable(Crawler $crawler, $tr)
    {
        return $crawler->filter('tbody > tr')
            ->eq($tr)
            ->filter('td')
            ->eq(1)
            ->filter('span')
            ->attr('data-value');
    }

    private function getTeamFromTable(Crawler $crawler, $tr, $td)
    {
        return $crawler
            ->filter('tbody > tr')
            ->eq($tr)
            ->filter('td')
            ->eq($td)
            ->filter('a')
            ->attr('title');
    }

    private function getScoreFromTable(Crawler $crawler, $tr)
    {
        return $crawler->filter('tbody > tr')
            ->eq($tr)
            ->filter('td')
            ->eq(3)
            ->filter('a')
            ->text();
    }
}
