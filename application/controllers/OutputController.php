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
      //require_once 'modules/Checklist/drawProcess.php';
      //$dp = new DrawProcess();
      //$dp->process_image($this);
      $this->process_image();
    }
    // exit();
  }

  public function process_image() {
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
    //logit("C: ". print_r($c, true));
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
    //logit("C: ". print_r($c, true));
    //logit('CCHART: '. $c->makeChart2(PNG));
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    # Output the chart
    $path = dirname(__DIR__) . '/../../public/tmp/';
    $fname = "{$path}savethis.png";
    logit("Fname: {$fname}");
    $c->makeChart($fname);
    //$this->getResponse()->setHeader("Content-type: image/png");
    ////print($c->makeChart2(PNG));
    //$this->getResponse()->setBody($c->makeChart2(PNG));
  }
}
