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

    private $qq0 = "Union of the two lists.";
    private $qq1 = "Intersection of the two lists.";
    private $qq2 = "Difference (trees - randomtrees).";

    private $trees;
    private $randomTrees;
    private $union;
    private $intersect;
    private $difference;

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

    private function treeToString(array $tree): string
    {
        return "id: " . $tree["id"] . ", name: " . $tree["name"] . ", height: " . $tree["height"] . ", home: " . $tree["home"] . ", price: " . $tree["price"] . ", age: " . $tree["age"];
    }

    /**
     * @Route(path="/list", name="treeList")
     * @param Request $request
     * @return Response
     */
    public function treeListAction(Request $request): Response
    {
        $this->loadTrees();
        foreach ($this->trees as $tree) {
            $twigParams["trees"][] = $tree;
        }

        $twigParams["ezquestions"][] = array("question" => $this->q0, "answer" => $this->Q0($this->trees));
        $twigParams["ezquestions"][] = array("question" => $this->q1, "answer" => $this->Q1($this->trees));
        $twigParams["ezquestions"][] = array("question" => $this->q2, "answer" => $this->Q2($this->trees));
        $twigParams["ezquestions"][] = array("question" => $this->q3, "answer" => $this->Q3($this->trees));
        $twigParams["ezquestions"][] = array("question" => $this->q4, "answer" => $this->Q4($this->trees));

        $this->randomizeEntries(20);
        foreach ($this->randomTrees as $tree) {
            $twigParams["randomtrees"][] = $tree;
        }

        $twigParams["qquestions"][] = array("question" => $this->qq0, "answer" => $this->QQ0());
        $twigParams["qquestions"][] = array("question" => $this->qq1, "answer" => $this->QQ1());
        $twigParams["qquestions"][] = array("question" => $this->qq2, "answer" => $this->QQ2());

        foreach ($this->union as $tree) {
            $twigParams["union"][] = $tree;
        }
        foreach ($this->intersect as $tree) {
            $twigParams["intersect"][] = $tree;
        }
        foreach ($this->difference as $tree) {
            $twigParams["difference"][] = $tree;
        }

        return $this->render('tree/list.html.twig', $twigParams);
    }

    private function Q0(array $trees): string
    {//highest
        $highest = 0;
        for ($i = 1; $i < count($trees); $i++) {
            if ($trees[$i]["height"] > $trees[$highest]["height"]) {
                $oldest = $i;
            }
        }
        return $this->treeToString($trees[$highest]);
    }

    private function Q1(array $trees): string
    {//oldest
        $oldest = 0;
        for ($i = 1; $i < count($trees); $i++) {
            if ($trees[$i]["age"] > $trees[$oldest]["age"]) {
                $oldest = $i;
            }
        }
        return $this->treeToString($trees[$oldest]);
    }

    private function Q2(array $trees): string
    {//2yo
        $num = 0;
        foreach ($trees as $tree) {
            if ($tree["age"] == 2) {
                $num++;
            }
        }
        return $num;
    }

    private function Q3(array $trees): string
    {//pine
        $num = 0;
        foreach ($trees as $tree) {
            if ($tree["name"] == "Pine") {
                $num++;
            }
        }
        return $num;
    }

    private function Q4(array $trees): string
    {//totalcost
        $sum = 0;
        foreach ($trees as $tree) {
            $sum = $sum + $tree["price"];
        }
        return $sum;
    }

    private function randomizeEntries(int $num)
    {
        $tree = array("id" => "", "name" => "", "height" => "", "home" => "", "price" => "", "age" => "");
        for ($i = 0; $i < $num; $i++) {
            $tree["id"] = $i;
            $tree["name"] = $this->trees[array_rand($this->trees)]["name"];
            $tree["height"] = rand(0, 30);
            $tree["home"] = $this->trees[array_rand($this->trees)]["home"];
            $tree["price"] = rand(0, 20000);
            $tree["age"] = rand(1, 20);
            $this->randomTrees[] = $tree;
        }
    }

    private function compareTrees(array $tree0, array $tree1): bool
    {
        return $tree0["name"] == $tree1["name"] &&
            $tree0["height"] == $tree1["height"] &&
            $tree0["home"] == $tree1["home"] &&
            $tree0["price"] == $tree1["price"] &&
            $tree0["age"] == $tree1["age"];
    }

    private function QQ0(): string
    {
        $union[] = $this->trees[0];
        foreach ($this->trees as $treeitem) {
            $putin = false;
            foreach ($union as $unioitem) {
                if (!$this->compareTrees($treeitem, $unioitem)) {
                    $putin = true;
                }
            }
            if ($putin) {
                $union[] = $treeitem;
            }
        }
        foreach ($this->randomTrees as $treeitem) {
            $putin = false;
            foreach ($union as $unioitem) {
                if (!$this->compareTrees($treeitem, $unioitem)) {
                    $putin = true;
                }
            }
            if ($putin) {
                $union[] = $treeitem;
            }
        }
        $this->union = $union;
        return count($union);
    }


    private function QQ1(): string
    {
        $intersect = array();
        foreach ($this->trees as $treeitem) {
            $putin = false;
            foreach ($this->randomTrees as $randomitem) {
                if ($this->compareTrees($treeitem, $randomitem)) {
                    $putin = true;
                }
            }
            if ($putin) {
                $intersect[] = $treeitem;
            }
        }
        $this->intersect = $intersect;
        return count($intersect);
    }

    private function QQ2(): string
    {
        $diff = array();

        foreach ($this->trees as $treeitem) {
            $putin = true;
            foreach ($this->randomTrees as $randomitem) {
                if ($this->compareTrees($treeitem, $randomitem)) {
                    $putin = false;
                }
            }
            if ($putin) {
                $diff[] = $treeitem;
            }
        }
        $this->difference = $diff;
        return count($diff);
    }
}