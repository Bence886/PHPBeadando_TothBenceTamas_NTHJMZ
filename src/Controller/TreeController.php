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

    private $q0 = "Which tree is the highest?";
    private $q1 = "Which tree is the oldest?";
    private $q2 = "How much 2 years old tree do we have?";
    private $q3 = "How much Pine trees do we have?";
    private $q4 = "How much is the total cost of our trees?";

    private $trees;
    private $randomTrees;

    private function loadTrees()
    {
        $file = file($this->FILENAME, FILE_IGNORE_NEW_LINES);
        $tree = array("id" => "", "name" => "", "height" => "", "home" => "", "price" => "", "age" => "");
        foreach ($file as $row) {
            $first = substr($row, 0, 1);
            $rest = substr($row, 1);
            if ($first == "%") {
                if ($tree["age"]) $this->trees[] = $tree;
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
        if ($tree["age"]) $this->trees[] = $tree;
    }

    private function treeToString(array $tree) : string
    {
        return "id: ".$tree["id"].", name: ".$tree["name"].", height: ".$tree["height"].", home: ".$tree["home"].", price: ".$tree["price"].", age: ".$tree["age"];
    }

    /**
     * @Route(path="/list", name="treeList")
     * @param Request $request
     * @return Response
     */
    public function treeListAction(Request $request): Response
    {
        $this->loadTrees();
        foreach ($this->trees as $tree)
        {
            $twigParams["trees"][]=$tree;
        }

        $twigParams["questions"][] = array("question" => $this->q0, "answer" => $this->Q0());
        $twigParams["questions"][] = array("question" => $this->q1, "answer" => $this->Q1());
        $twigParams["questions"][] = array("question" => $this->q2, "answer" => $this->Q2());
        $twigParams["questions"][] = array("question" => $this->q3, "answer" => $this->Q3());
        $twigParams["questions"][] = array("question" => $this->q4, "answer" => $this->Q4());

        $this->randomizeEntries(20);
        foreach ($this->randomTrees as $tree)
        {
            $twigParams["randomtrees"][]=$tree;
        }

        return $this->render('tree/list.html.twig', $twigParams);
    }

    private function Q0(): string
    {//highest
        $highest = 0;
        for ($i = 1; $i < count($this->trees); $i++)
        {
            if ($this->trees[$i]["height"] > $this->trees[$highest]["height"])
            {
                $oldest = $i;
            }
        }
        return $this->treeToString($this->trees[$highest]);
    }

    private function Q1(): string
    {//oldest
        $oldest = 0;
        for ($i = 1; $i < count($this->trees); $i++)
        {
            if ($this->trees[$i]["age"] > $this->trees[$oldest]["age"])
            {
                $oldest = $i;
            }
        }
        return $this->treeToString($this->trees[$oldest]);
    }

    private function Q2(): string
    {//2yo
        $num = 0;
        foreach ($this->trees as $tree) {
            if ($tree["age"] == 2)
            {
                $num++;
            }
        }
        return $num;
    }

    private function Q3(): string
    {//pine
        $num = 0;
        foreach ($this->trees as $tree) {
            if ($tree["name"]== "Pine")
            {
                $num++;
            }
        }
        return $num;
    }

    private function Q4(): string
    {//totalcost
        $sum = 0;
        foreach ($this->trees as $tree) {
            $sum = $sum + $tree["price"];
        }
        return $sum;
    }

    private function randomizeEntries(int $num)
    {
        $tree = array("id" => "", "name" => "", "height" => "", "home" => "", "price" => "", "age" => "");
        for ($i = 0; $i < $num; $i++)
        {
            $tree["id"] = $i;
            $tree["name"] = array_rand($this->trees)["name"];
            $tree["height"] = rand(0, 30);
            $tree["home"] = array_rand($this->trees)["home"];
            $tree["price"] = rand(0, 20000);
            $tree["age"] = rand(1, 20);
            $this->randomTrees[] = $tree;
        }
    }
}