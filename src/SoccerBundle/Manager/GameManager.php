<?php

namespace SoccerBundle\Manager;

use Doctrine\ORM\EntityManager;
use SoccerBundle\Entity\Game;
use SoccerBundle\Manager\StandingsManager;

class GameManager
{
    protected $em;

    protected $standingsManager;

    public function __construct(EntityManager $entityManager, StandingsManager $standingsManager)
    {
        $this->em = $entityManager;
        $this->standingsManager = $standingsManager;
    }

    public function setGameIfNotExist($date, $homeTeam, $awayTeam, $scoreArray)
    {
        $game = new Game();
        $game->setDate($date);
        $game->setHomeTeam($homeTeam);
        $game->setAwayTeam($awayTeam);
        $game->setHomeTeamScore($scoreArray['homeScore']);
        $game->setAwayTeamScore($scoreArray['awayScore']);

        if (!$this->isExistThisGame($game)) {
            $this->standingsManager->removeStandings($date);

            $this->em->persist($game);
            $this->em->flush();
        }
    }

    public function isExistThisGame(Game $game)
    {
        if ($game->getHomeTeam()->getId() && $game->getAwayTeam()->getId()) {
            return $this->em->getRepository('SoccerBundle:Game')->findOneBy(array(
                'homeTeam' => $game->getHomeTeam(),
                'awayTeam' => $game->getAwayTeam(),
                'date' => $game->getDate(),
            ));
        }
        return false;
    }
}