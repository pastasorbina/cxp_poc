<?php

if (!extension_loaded("gd")) dl("gd.so");

function piechart($data, $title = "Pie Chart" , $labels, $w = 400, $h = 300) {
	include ("jpgraph/jpgraph.php");
	include ("jpgraph/jpgraph_pie.php");
	include ("jpgraph/jpgraph_pie3d.php");
	
// 	$data = array(40,60,21,33);
	
	$graph = new PieGraph($w,$h,"auto");
	//$graph->SetShadow();
	
	$graph->title->Set($title);
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	
	$p1 = new PiePlot3D($data);
	$p1->SetSize(0.4);
	$p1->SetCenter(0.4);
	$p1->SetLegends($labels);
	
 	$p1->SetSliceColors(array('greenyellow' , 'pink', 'lightblue' , 'antiquewhite', 'orange' ,'yellowgreen' ,'tan')); 
 	//$p1->SetSliceColors(array( '#FF3300', '#FF7100', '#FF9700', '#FFC300', '#FFF000', '#BFE200', '#60BF00', '#00AF0C', '#00B2C2', '#0070B7', '#0035AD', '#1500A6', '#7300A6', '#C30087', '#EF002A')); 
	
	$graph->Add($p1);
	$graph->Stroke();
}

function novote() {
	include "jpgraph/jpgraph.php";
	include "jpgraph/jpgraph_canvas.php";
	
	// Setup a basic canvas we can work 
	$g = new CanvasGraph(300,60,'auto' );
	$g->SetMargin( 5,11,6 ,11);
	$g->SetShadow();
	$g->SetMarginColor( "pink");
	
	// We need to stroke the plotarea and margin before we add the
	// text since we otherwise would overwrite the text.
	$g->InitFrame();
	
	// Draw a text box in the middle
	$txt="No one vote for this";
	$t = new Text( $txt,150,20 );
	$t->SetFont( FF_ARIAL, FS_BOLD,12);
	
	// How should the text box interpret the coordinates?
	$t->Align( 'center','top');
	
	// How should the paragraph be aligned?
	$t->ParagraphAlign( 'center');
	
	// Add a box around the text, white fill, black border and gray shadow
	$t->SetBox( "white", "black","gray");
	
	// Stroke the text
	$t->Stroke( $g->img);
	
	// Stroke the graph
	$g->Stroke(); 
}

function groupbarchart($data1y , $data2y , $title = "Title" , $legend1 = "" , $legend2 = "", $xtitle = "x-title", $ytitle = "y-title") {
	include ("jpgraph/jpgraph.php");
	include ("jpgraph/jpgraph_bar.php");
	
	// Create the graph. These two calls are always required
	$graph = new Graph(310,300,"auto");    
	$graph->SetScale("textlin");
	
	$graph->SetShadow();
	$graph->img->SetMargin(40,30,120,40);
	$graph->yaxis->scale->SetGrace(10);
	$graph->SetTickDensity(TICKD_SPARSE);
	
	// Create the bar plots
	$b1plot = new BarPlot($data1y);
	$b1plot->SetFillColor("greenyellow");
	$b1plot->SetLegend ($legend1); 
	$b2plot = new BarPlot($data2y);
	$b2plot->SetFillColor("pink");
	$b2plot->SetLegend ($legend2); 
	
	// Create the grouped bar plot
	$gbplot = new GroupBarPlot(array($b1plot,$b2plot));
	
	// ...and add it to the graPH
	$graph->Add($gbplot);
	
	$graph->title->Set($title);
	$graph->xaxis->title->Set($xtitle);
	$graph->yaxis->title->Set($ytitle);
	
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
	
	// Display the graph
	$graph->Stroke();
}

function sm_timeline($data , $title) {
	include ("jpgraph/jpgraph.php");
	include ("jpgraph/jpgraph_bar.php");
	
	// Create the graph. These two calls are always required
	$graph = new Graph(930,200,"auto");    
	$graph->SetScale("textlin");
	
	$graph->SetShadow();
	
	// Create the bar plots
	$b1plot = new BarPlot($data);
	$b1plot->SetFillColor("greenyellow");
	
	// Setup values
	$b1plot->value->Show();
	$b1plot->value->SetFormat('%d');
	$b1plot->value->SetFont(FF_FONT1,FS_BOLD);
	
	// Center the values in the bar
	$b1plot->SetValuePos('center');		
	
	// ...and add it to the graPH
	$graph->Add($b1plot);
	
	$graph->title->Set($title);
	$graph->xaxis->title->Set("Hour");
	$graph->yaxis->title->Set("Sales");
	
	$lbl = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);
	$graph->xaxis->SetTickLabels($lbl);	
	
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
	
	// Display the graph
	$graph->Stroke();
}

function sm_pitch_performance($data , $title , $label) {
	include ("jpgraph/jpgraph.php");
	include ("jpgraph/jpgraph_bar.php");
	
	// Create the graph. These two calls are always required
	$graph = new Graph(930,200,"auto");    
	$graph->SetScale("textlin");
	
	$graph->SetShadow();
	
	// Create the bar plots
	$b1plot = new BarPlot($data);
	$b1plot->SetFillColor("greenyellow");
	
	// Setup values
	$b1plot->value->Show();
	$b1plot->value->SetFormat('%d');
	$b1plot->value->SetFont(FF_FONT1,FS_BOLD);
	
	// Center the values in the bar
	$b1plot->SetValuePos('center');		
	
	// ...and add it to the graPH
	$graph->Add($b1plot);
	
	$graph->title->Set($title);
	$graph->xaxis->title->Set("Price");
	$graph->yaxis->title->Set("Playing Hour");
	
	$lbl = $label;
	$graph->xaxis->SetTickLabels($lbl);
	
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
	
	// Display the graph
	$graph->Stroke();
}


function piechart_dashboard($data, $title = "Pie Chart" , $labels, $w = 400, $h = 300) {
	include ("jpgraph/jpgraph.php");
	include ("jpgraph/jpgraph_pie.php");
	include ("jpgraph/jpgraph_pie3d.php");
	
// 	$data = array(40,60,21,33);
	
	$graph = new PieGraph($w,$h,"auto");
	//$graph->SetShadow();
	
	$graph->title->Set($title);
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	//$graph->title->SetFont(FF_VERDANA,FS_BOLD,12); 
	$graph->title->SetColor("#3D4B81");
	$graph->SetFrame(false);
	
	$p1 = new PiePlot3D($data);
	$p1->SetSize(0.35);
	$p1->SetCenter(0.5);
	//$p1->SetLegends($labels);
	$p1->SetLabels($labels);
	$p1->SetLabelPos(1);

	//$p1->value->Show();

 	$p1->SetSliceColors(array('greenyellow' , 'pink', 'lightblue' , 'antiquewhite', 'orange' ,'yellowgreen' ,'tan', 'red'));
 	//$p1->SetSliceColors(array( '#FF3300', '#FF7100', '#FF9700', '#FFC300', '#FFF000', '#BFE200', '#60BF00', '#00AF0C', '#00B2C2', '#0070B7', '#0035AD', '#1500A6', '#7300A6', '#C30087', '#EF002A')); 
	
	$graph->Add($p1);
	$graph->Stroke();
}

function horizontalbar_dashboard($data, $title, $labels, $w = 400, $h = 300) {
	require_once ('jpgraph/jpgraph.php');
	require_once ('jpgraph/jpgraph_bar.php');
	
	// Set the basic parameters of the graph
	$graph = new Graph($w, $h);
	$graph->SetScale('textlin');
	
	$top = 60;
	$bottom = 30;
	$left = 80;
	$right = 20;
	$graph->Set90AndMargin($left,$right,$top,$bottom);
	
	// Nice shadow
	//$graph->SetShadow();
	
	// Setup labels
	$graph->xaxis->SetTickLabels($labels);
	
	// Label align for X-axis
	$graph->xaxis->SetLabelAlign('right','center','right');
	
	// Label align for Y-axis
	$graph->yaxis->SetLabelAlign('center','bottom');
	
	// Titles
	$graph->title->Set($title);
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->title->SetColor("#3D4B81");
	$graph->SetFrame(false);
	
	// Create a bar pot
	$bplot = new BarPlot($data);
	$bplot->SetFillColor('greenyellow');
	$bplot->SetWidth(0.5);
	$bplot->value->Show();
	//$bplot->value->SetFont(FF_ARIAL,FS_BOLD,12);
	//$bplot->value->SetAlign('left','center');
	//$bplot->value->SetColor('black','darkred');
	$bplot->value->SetFormat('%.1f');

	
	$graph->Add($bplot);
	
	$graph->Stroke();

}

function bar_dashboard($data, $title, $w = 400, $h = 300) {
	require_once ('jpgraph/jpgraph.php');
	require_once ('jpgraph/jpgraph_bar.php');
	
  // Create the graph and setup the basic parameters
  $graph = new Graph($w,$h,'auto');
  $graph->img->SetMargin(50,30,30,40);
  $graph->SetScale('textint');
  $graph->SetFrame(false); // No border around the graph
  
  // Add some grace to the top so that the scale doesn't
  // end exactly at the max value.
  // The grace value is the percetage of additional scale
  // value we add. Specifying 50 means that we add 50% of the
  // max value
  $graph->yaxis->scale->SetGrace(20);
  
  // Setup X-axis labels
  $a = $gDateLocale->GetShortMonth();
  $graph->xaxis->SetTickLabels($a);
  //$graph->xaxis->SetFont(FF_FONT2);
  
  // Setup graph title ands fonts
  $graph->title->Set($title);
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->title->SetColor("#3D4B81");
  
  // Create a bar pot
	$bplot = new BarPlot($data);
	$bplot->SetFillColor('orange');
	$bplot->SetWidth(0.5);
	$bplot->value->SetFormat('%d');  
  
  // Setup the values that are displayed on top of each bar
  $bplot->value->Show();
  
  // Must use TTF fonts if we want text at an arbitrary angle
  //$bplot->value->SetFont(FF_ARIAL,FS_BOLD);
  //$bplot->value->SetAngle(45);
  
  // Black color for positive values and darkred for negative values
  $bplot->value->SetColor('black','darkred');
  $graph->Add($bplot);
  
  // Finally stroke the graph
  $graph->Stroke();	

}

function barline_dashboard($data, $dataline1, $dataline2, $title, $w = 400, $h = 300) {
	require_once ('jpgraph/jpgraph.php');
	require_once ('jpgraph/jpgraph_bar.php');
	require_once ('jpgraph/jpgraph_line.php');
	
  // Create the graph and setup the basic parameters
  $graph = new Graph($w,$h,'auto');
  $graph->img->SetMargin(50,30,30,40);
  $graph->SetScale('textint');
  $graph->SetFrame(false); // No border around the graph
  
  // Add some grace to the top so that the scale doesn't
  // end exactly at the max value.
  // The grace value is the percetage of additional scale
  // value we add. Specifying 50 means that we add 50% of the
  // max value
  $graph->yaxis->scale->SetGrace(20);
  
  // Setup X-axis labels
  $a = $gDateLocale->GetShortMonth();
  $graph->xaxis->SetTickLabels($a);
  //$graph->xaxis->SetFont(FF_FONT2);
  
  // Setup graph title ands fonts
  $graph->title->Set($title);
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->title->SetColor("#3D4B81");
  
  // Create a bar pot
  $bplot = new BarPlot($data);
  $bplot->SetFillColor('skyblue@0.5');
  $bplot->SetWidth(0.5);
  $bplot->SetLegend('Sales');
  
  // Setup the values that are displayed on top of each bar
  $bplot->value->Show();
  
  // Must use TTF fonts if we want text at an arbitrary angle
  //$bplot->value->SetFont(FF_ARIAL,FS_BOLD);
  //$bplot->value->SetAngle(45);
  
  
	// Create filled line plot #1
	$lplot1 = new LinePlot($dataline1);
	//$lplot1->SetFillColor('skyblue@0.5');
	$lplot1->SetColor('navy@0.7');
	$lplot1->SetBarCenter();
	$lplot1->SetWeight(2);
	
	$lplot1->mark->SetType(MARK_SQUARE);
	$lplot1->mark->SetColor('blue@0.5');
	$lplot1->mark->SetFillColor('lightblue');
	$lplot1->mark->SetSize(6);
	$lplot1->SetLegend('Trend');
	
	$graph->Add($lplot1);

	// Create filled line plot #2
	$lplot2 = new LinePlot($dataline2);
	//$lplot2->SetFillColor('red@0.9');
	$lplot2->SetColor('maroon');
	$lplot2->SetBarCenter();
	$lplot2->SetWeight(2);
	
	$lplot2->mark->SetType(MARK_SQUARE);
	$lplot2->mark->SetColor('red@0.5');
	$lplot2->mark->SetFillColor('red@0.5');
	$lplot2->mark->SetSize(6);
	$lplot2->SetLegend('Target');
	
	$graph->Add($lplot2);

  // Black color for positive values and darkred for negative values
  $bplot->value->SetColor('black','darkred');
  $graph->Add($bplot);
  
	$graph->legend->SetLayout(LEGEND_HOR);
	$graph->legend->Pos(0.5,0.99,"center","bottom");
  // Finally stroke the graph
  $graph->Stroke();

}



?>