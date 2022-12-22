<?php

namespace App\Service;


use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Plot\BarPlot;
use App\Repository\DocumentRepository;
use Amenadiel\JpGraph\Themes\AquaTheme;
use Amenadiel\JpGraph\Plot\GroupBarPlot;
use Amenadiel\JpGraph\Themes\VividTheme;
use Amenadiel\JpGraph\Graph\Graph as GraphGraph;

class GraphiqueService
{
    public function __construct(
        private DocumentRepository $documentRepository
        ){
    }

    public function graphCA_Annuel($annee){
        $totaux = [];
            for($m=1;$m<=12;$m++){
                $result = $this->documentRepository->sumFactureByMonth($m,$annee);
                $result_100 = number_format($result / 100,2);
                array_push($totaux,$result_100);
            }
        $totalAnnuel = array_sum($totaux);

        $data1y=$totaux;
        
        // Create the graph. These two calls are always required
        $graph = new GraphGraph(1050,600,'auto');
        $graph->SetScale("textlin");
        
        $theme_class = new VividTheme;
        $graph->SetTheme($theme_class);
        
        $graph->yaxis->SetTextTickInterval(1,2);
        $graph->SetBox(false);
        
        $graph->ygrid->SetFill(false);
        $graph->xaxis->SetTickLabels(array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre'));
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false,false);
        
        // Create the bar plots
        $b1plot = new BarPlot($data1y);
        $b1plot->SetLegend($annee);
        
        
        // Create the grouped bar plot
        $gbplot = new GroupBarPlot(array($b1plot));
        // ...and add it to the graPH
        $graph->Add($gbplot);
        $graph->legend->SetPos(0.5,0.92,'center','bottom');
        
        
        $b1plot->SetColor("white");
        $b1plot->SetFillColor("#cc1111");
        $b1plot->value->Show();
        
        $graph->title->Set("Ventes par mois en ".$annee." \n Total HT: ".$totalAnnuel);
        
        // Display the graph
        $graph->Stroke();
    }

    public function graphCA_Annuel_Comparaison($annee1,$annee2){
        $totaux1y = [];
        for($m=1;$m<=12;$m++){
            $result = $this->documentRepository->sumFactureByMonth($m,$annee1);
            $result_100 = number_format($result / 100,2);
            array_push($totaux1y,$result_100);
        }
        $data1y=$totaux1y;

        $totaux2y = [];
        for($m=1;$m<=12;$m++){
            $result = $this->documentRepository->sumFactureByMonth($m,$annee2);
            $result_100 = number_format($result / 100,2);
            array_push($totaux2y,$result_100);
        }
        $data2y=$totaux2y;

        // Create the graph. These two calls are always required
        $graph = new Graph(1050,600,'auto');
        $graph->SetScale("textlin");

        //choix du theme
        $theme_class = new AquaTheme;
        $graph->SetTheme($theme_class);

        //axe des Y
        //$graph->yaxis->SetTickPositions(array(0,30,60,90,120,150,180,210,240,270,300), array(15,45,75,105,135,165,195,225));
        $graph->yaxis->SetTextTickInterval(1,2);
        $graph->SetBox(false);

        $graph->ygrid->SetFill(false);
        $graph->xaxis->SetTickLabels(array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre'));
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false,false);

        // Create the bar plots
        $b1plot = new BarPlot($data2y);
        $b1plot->SetLegend($annee1);
        $b2plot = new BarPlot($data1y);
        $b2plot->SetLegend($annee2);

        // Create the grouped bar plot
        $gbplot = new GroupBarPlot(array($b1plot,$b2plot));
        // ...and add it to the graPH
        $graph->Add($gbplot);
        $graph->legend->SetPos(0.5,0.92,'center','bottom');


        $b1plot->SetColor("white");
        $b1plot->SetFillColor("#cc1111");
        $b1plot->value->Show();

        $b2plot->SetColor("white");
        $b2plot->SetFillColor("#11cccc");
        $b2plot->value->Show();

        $graph->title->Set("Ventes par mois (HT) ".$annee1." / ".$annee2);


        // Display the graph
        $graph->Stroke();
    }
}
