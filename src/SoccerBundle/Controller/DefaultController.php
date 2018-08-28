<?php

namespace SoccerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        if (null != $request->query->get('teamName')) {
            $teamName = $request->query->get('teamName');
            $team = $this->getDoctrine()
                ->getRepository('SoccerBundle:Team')
                ->findOneByName($teamName);

            if ($team) {
            $games = $this->getDoctrine()
                ->getRepository('SoccerBundle:Game')
                ->getAllGamesByTeam($team);
            }
            else {
                $this->get('session')->getFlashBag()->add('notice', 'Invalid team name');
                $games = array();
            }
        }
        else {
            $games = $this->getDoctrine()
                ->getRepository('SoccerBundle:Game')
                ->getAllGames();
        }

        return $this->render('SoccerBundle:Default:index.html.twig', array(
            'games' => $games,
        ));
    }


}
