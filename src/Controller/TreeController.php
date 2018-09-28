<?php
/**
 * Created by PhpStorm.
 * User: tbenc
 * Date: 2018. 09. 28.
 * Time: 8:59
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/tree")
 * Class TreeController
 * @package App\Controller
 */
class TreeController extends Controller
{
    private $FILENAME = "../templates/trees.txt";
    /**
     * @Route(path="/list", name="treeList")
     * @param Request $request
     * @return Response
     */
    public function treeListAction(Request $request): Response
    {
        $trees = file($this->FILENAME, FILE_IGNORE_NEW_LINES);
        $tree = array("id" => "", "name" => "", "height" => "", "home" => "", "price" => "", "age" => "");
        foreach ($trees as $row) {
            $first = substr($row, 0, 1);
            $rest = substr($row, 1);
            if ($first == "%") {
                if ($tree["age"]) $twigParams["trees.txt"][] = $tree;
                $tree["id"] = $rest;
            } else if ($first == "&") {
                $tree["name"] = $rest;
            } else if ($first == "#") {
                $tree["height"] = $rest;
            } else if ($first == "@") {
                $tree["home"] = $rest;
            } else if ($first == "!") {
                $tree["price"] = $rest;
            } else if ($first == "?") {
                $tree["age"] = $rest;
            }
        }
        if ($tree["age"]) $twigParams["trees"][] = $tree;
        return $this->render('tree/list.html.twig', $twigParams);
    }
}