<?php
require_once 'modules/Checklist/logger.php';
require_once 'modules/Checklist/datefns.php';
require_once 'modules/Checklist/general.php';
require_once 'modules/Checklist/processor.php';
require_once '../application/controllers/ActionController.php';
require_once 'modules/ChartDirector/lib/phpchartdir.php';

class OutputController extends Application_Controller_Action {
  public $debug = 0;

  public function init() {
    /* Initialize action controller here */
    // logit("MT aud init: ". microtime(true));
    parent::init();
  }

  public function indexAction() {
  }

  public function processAction() {
    // process the selected action
    $this->dialog_name = 'audit/select';
    logit("ProDialog Name: {$this->dialog_name}");
    $proc = new Processing();
    $prefix = 'cb_';
    if ($this->collectData())
      return;
    $todo = $this->data['todo'];

    logit('OutData: ' . print_r($this->data, true));
    $this->collectExtraData($prefix);
    logit('OutData: ' . print_r($this->extra, true));
    $list = array();
    foreach($this->extra as $n => $v) {
      $list[] = (int) substr($n, 3);
      //logit('LIST: '. print_r($list, true));
    }
    $name = $todo;
    $proc->process($list, $name);
    $this->echecklistNamespace->flash = 'Excel sheet done.';
    $this->_redirector->gotoUrl($this->mainpage);
  }

  public function graphicAction() {
    // process the url to see what is to be shown
    $url = $this->_request->getRequestUri();
    $part = explode("?", $url);
    logit("URL: {$url} " . print_r($part, true));
    $pinfo = explode("/", $part[0]);
    $args = $part[1];
    logit('Vars: ' . print_r($pinfo, true));
    if ($pinfo[count($pinfo) - 1] == 'graphic') {
      $list = explode(',', $args);
      $name = $list[0];
      unset($list[0]);
      logit(print_r($list, true));
        // time to call the code
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);
      switch ($name) {
        case 'incomplete' :
          $this->stacked_barchart();
          break;
        case 'compare' :
          $this->parallel_barchart();
          break;
        case 'auditspider' :
          $this->spider_chart();
          break;
        default :
      }
    }
    // exit();
  }

  public function spider_chart() {
      // this is a fully localized test image - uses not outside info
      # Data for the chart
    $data0 = array(70,20,42,23,37,41,34,18,6,16,22,35,47);
    if (false) {
      $data1 = array(65,36,58,19,89,19,72,23,78,49,33,45,68);
      $data2 = array(40,92,76,47,86,27,68,11,97,55,96,22,74);
    }
    $labels = array("Total","Section<*br*>1","Section<*br*>2","Section<*br*>3","Section<*br*>4",
        "Section<*br*>5","Section<*br*>6","Section<*br*>7","Section<*br*>8","Section<*br*>9",
        "Section<*br*>10","Section<*br*>11","Section<*br*>12");

    $c = new PolarChart(660, 700, 0xe0e0e0, 0x000000, 1);

    $textBoxObj = $c->addTitle("SLIPTA Audit Scoring", "arialbi.ttf", 15);
    //$textBoxObj->setBackground($c->patternColor(dirname(__FILE__)."/wood.png"));


    # Set center of plot area at (230, 280) with radius 180 pixels, and white (ffffff)
    # background.
    $c->setPlotArea(330, 350, 235, 0xffffff);

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
    $c->addAreaLayer($data0, 0x80ff0000, "Lab A");
    if (false) {
      $c->addAreaLayer($data1, 0x8000ff00, "Lab B");
      $c->addAreaLayer($data2, 0x800000ff, "lab C");
    }
    // $this->_helper->layout->disableLayout();
    // $this->_helper->viewRenderer->setNoRender(true);
    # Output the chart
    //$path = dirname(__DIR__) . '/../public/tmp/';
    //$fname = "{$path}savethis.png";
    //$this->getResponse()->setHeader("Content-type: image/png");
    //$this->getResponse()->setBody($c->makeChart2(PNG));
    header("Content-type: image/png");
    print($c->makeChart2(PNG));
  }

  public function stacked_barchart() {
    # The data for the bar chart
    $data0 = array(70, 22, 45, 67, 23, 13, 59, 63, 34, 12, 13, 15, 17);
    $data1 = array(70, 22, 45, 67, 23, 13, 59, 63, 34, 12, 13, 15, 17);
    $data2 = array(70, 22, 45, 67, 23, 13, 59, 63, 34, 12, 13, 15, 17);

    # The labels for the bar chart
    $labels = array("All","Section 1","Section 2","Section 3","Section 4","Section 5","Section 6",
    "Section 7","Section 8","Section 9","Section 10","Section 11","Section 12",);

    # Create a XYChart object of size 500 x 320 pixels
    $c = new XYChart(700, 420);

    # Set the plotarea at (100, 40) and of size 280 x 240 pixels
    $c->setPlotArea(80, 90, 480, 240);

    # Add a legend box at (400, 100)
    $c->addLegend(80, 40)->setCols(3); //400, 100);

    # Add a title to the chart using 14 points Times Bold Itatic font
    $c->addTitle("Completeness Levels - by section", "timesbi.ttf", 14);

    # Add a title to the y axis. Draw the title upright (font angle = 0)
    $textBoxObj = $c->yAxis->setTitle("Items Counts");
    $textBoxObj->setFontAngle(90);

    # Set the labels on the x axis
    $c->xAxis->setLabels($labels)->setFontAngle(45);

    # Add a stacked bar layer and set the layer 3D depth to 8 pixels
    $layer = $c->addBarLayer2(Stack, 0);

    # Add the three data sets to the bar layer

    $layer->addDataSet($data2, 0x00ff00, "Yes");
    $layer->addDataSet($data1, 0xff0000, "No");
    $layer->addDataSet($data0, 0xffffff, "Not Answered");

    # Enable bar label for the whole bar
    $layer->setAggregateLabelStyle();

    # Enable bar label for each segment of the stacked bar
    $layer->setDataLabelStyle();

    # Output the chart
    header("Content-type: image/png");
    print($c->makeChart2(PNG));
  }

  public function parallel_barchart() {
    # The data for the bar chart
    $data0 = array(70, 22, 45, 67, 23, 13, 59, 63, 34, 12, 13, 15, 17);
    $data1 = array(23, 13, 59, 63, 34, 12, 13, 15, 17, 70, 22, 45, 67);
    $data2 = array(34, 12, 13, 15, 17, 70, 22, 45, 67, 23, 13, 59, 63);

    # The labels for the bar chart
    $labels = array("All","Section 1","Section 2","Section 3","Section 4","Section 5","Section 6",
    "Section 7","Section 8","Section 9","Section 10","Section 11","Section 12",);

    # Create a XYChart object of size 500 x 320 pixels
    $c = new XYChart(700, 420);

    # Set the plotarea at (100, 40) and of size 280 x 240 pixels
    $c->setPlotArea(80, 90, 580, 240);

    # Add a legend box at (400, 100)
    $c->addLegend(80, 40)->setCols(3); //400, 100);

    # Add a title to the chart using 14 points Times Bold Itatic font
    $c->addTitle("Completeness Levels - by section", "timesbi.ttf", 14);

    # Add a title to the y axis. Draw the title upright (font angle = 0)
    $textBoxObj = $c->yAxis->setTitle("Items Counts");
    $textBoxObj->setFontAngle(90);

    # Set the labels on the x axis
    $c->xAxis->setLabels($labels)->setFontAngle(45);

    # Add a stacked bar layer and set the layer 3D depth to 8 pixels
    $layer = $c->addBarLayer2();
    // set the bar gap
    $layer->setBarGap(0.4, TouchBar);
    # Add the three data sets to the bar layer

    $layer->addDataSet($data2, 0x00ff00, "Yes");
    $layer->addDataSet($data1, 0xff0000, "No");
    $layer->addDataSet($data0, 0xffffff, "Not Answered");

    # Enable bar label for the whole bar
    $layer->setAggregateLabelStyle();

    # Enable bar label for each segment of the stacked bar
    $layer->setDataLabelStyle();

    # Output the chart
    header("Content-type: image/png");
    print($c->makeChart2(PNG));
  }
}
