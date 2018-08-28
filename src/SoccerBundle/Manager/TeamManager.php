<?php

namespace SoccerBundle\Manager;

use Doctrine\ORM\EntityManager;
use SoccerBundle\Entity\Team;

class TeamManager
{
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getTeamByName($name)
    {
        $homeTeam = $this->em->getRepository('SoccerBundle:Team')->findOneByName($name);

        if (!$homeTeam) {
            $homeTeam = new Team();
            $homeTeam->setName($name);

            $this->em->persist($homeTeam);
            $this->em->flush();
        }

        return $homeTeam;
    }
}