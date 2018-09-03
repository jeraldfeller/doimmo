<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Athome Scraper</title>
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/main.css?v=1.4">
  <!-- Resources -->
  <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
  <script src="https://www.amcharts.com/lib/3/serial.js"></script>
  <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
  <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all"/>
  <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
  <style>
    #chartdiv {
      width: 100%;
      height: 500px;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
  <span class="navbar-brand mb-0 h1">Athome Scraper</span>
</nav>
<div class="container-fluid">
  <div class="row table-row">
    <div class="col-md-2">
      <h4>1. Schedule</h4>
      <span class="last-schedule"><i class="fa fa-circle-o-notch fa-spin"></i></span>
      <br>
      <a href="view.php?table=history" target="_blank" class="btn btn-primary pull-left" id="exportBtn"><i class="fa fa-table"></i> View Table</a>
    </div>
  </div>
  <div class="row table-row">
    <div class="col-md-6">
      <h4>2. Manual</h4>
      <button class="btn btn-primary" id="scrapeNowBtn"><i class="fa fa-play"></i> Scrape Now</button>
      <a href="view.php?table=manual" target="_blank" class="btn btn-primary" id="exportBtn"><i class="fa fa-table"></i> View Table</a>
    </div>

    <div class="col-md-12 spacer status-container display-none">
      Status: <span class="status"><i class="fa fa-circle-o-notch fa-spin"></i></span>
      <div class="progress display-none">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">0%</div>
      </div>
    </div>
  </div>


</div>


<script type="text/javascript" src="assets/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript">
  $(document).ready(function () {
    $.ajax({
      url: 'Controller/api.php?action=get-last-schedule',
      type: 'get',
      dataType: 'json',
      success: function (r) {
        console.log(r);
        $('.last-schedule').html('last schedule: '+r);
      }
    });
    $interval = setInterval(function () {
      $.ajax({
        url: 'Controller/api.php?action=check-process',
        type: 'get',
        dataType: 'json',
        success: function (r) {
          $curPage = r.current_page - 1;
          $percent = ($curPage / r.total_page) * 100;
          if($percent < 100){
            $('.status-container').removeClass('display-none');
          }
          $('.progress').removeClass('display-none');
          if (r.active == 1) {
            $status = 'Active / Page ' + $curPage + ' of ' + r.total_page + ' / '+$percent.toFixed()+'%';
          } else {
            $status = 'Complete / Page ' + $curPage + ' of ' + r.total_page  + ' / '+$percent.toFixed()+'%';
          }

          $('.progress-bar').attr('aria-valuenow', $percent.toFixed()).css('width', $percent.toFixed()+"%").text($percent.toFixed()+'%');

          $('.status').html($status);
        }
      });
    }, 5000);

    $('#scrapeNowBtn').click(function () {
      $btn = $(this);
      $btn.attr('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i>');
      $.ajax({
        url: 'Controller/api.php?action=scan',
        type: 'get',
        dataType: 'json',
        success: function (r) {
          $btn.removeAttr('disabled').html('<i class="fa fa-play"></i> Scrape Now');
          $('.status-container').removeClass('display-none');
          $('.progress-bar').attr('aria-valuenow', 0).css('width', 0).text('0%');
          alert('Scraper is now running');
        }
      });
    });
  });
</script>
<!-- Chart code -->

</body>

</html>