<?php
// This file handles the creation of graphical output

require_once 'modules/Checklist/logger.php';
require_once 'modules/Checklist/processCommon.php';
require_once 'modules/ChartDirector/lib/phpchartdir.php';

class DrawProcess extends Process_Common {

  public function process_image($x) {
  // this is a fully localized test image - uses not outside info
    # Data for the chart
    $data0 = array(5, 3, 10, 4, 3, 5, 2, 5);
    $data1 = array(12, 6, 17, 6, 7, 9, 4, 7);
    $data2 = array(17, 7, 22, 7, 18, 13, 5, 11);

    $labels = array("North", "North<*br*>East", "East", "South<*br*>East", "South",
        "South<*br*>West", "West", "North<*br*>West");

    # Create a PolarChart object of size 460 x 500 pixels, with a grey (e0e0e0)
    # background and 1 pixel 3D border
    $c = new PolarChart(460, 500, 0xe0e0e0, 0x000000, 1);
logit("C: ". print_r($c, true));
    # Add a title to the chart at the top left corner using 15pts Arial Bold Italic font.
    # Use a wood pattern as the title background.
    $textBoxObj = $c->addTitle("Polar Area Chart Demo", "arialbi.ttf", 15);
    //$textBoxObj->setBackground($c->patternColor(dirname(__FILE__)."/wood.png"));

    # Set center of plot area at (230, 280) with radius 180 pixels, and white (ffffff)
    # background.
    $c->setPlotArea(230, 280, 180, 0xffffff);

    # Set the grid style to circular grid
    $c->setGridStyle(false);

    # Add a legend box at top-center of plot area (230, 35) using horizontal layout. Use
    # 10 pts Arial Bold font, with 1 pixel 3D border effect.
    $b = $c->addLegend(230, 35, false, "arialbd.ttf", 9);
    $b->setAlignment(TopCenter);
    $b->setBackground(Transparent, Transparent, 1);

    # Set angular axis using the given labels
    $c->angularAxis->setLabels($labels);

    # Specify the label format for the radial axis
    $c->radialAxis->setLabelFormat("{value}%");

    # Set radial axis label background to semi-transparent grey (40cccccc)
    $textBoxObj = $c->radialAxis->setLabelStyle();
    $textBoxObj->setBackground(0x40cccccc, 0);

    # Add the data as area layers
    $c->addAreaLayer($data2, -1, "5 m/s or above");
    $c->addAreaLayer($data1, -1, "1 - 5 m/s");
    $c->addAreaLayer($data0, -1, "less than 1 m/s");
    logit("C: ". print_r($c, true));
    logit('CCHART: '. $c->makeChart2(PNG));
    $x->_helper->layout->disableLayout();
    $x->_helper->viewRenderer->setNoRender(true);
    # Output the chart
    $x->getResponse()->setHeader("Content-type: image/png");
    //print($c->makeChart2(PNG));
    $x->getResponse()->setBody($c->makeChart2(PNG));
  }
}