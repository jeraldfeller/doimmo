<?php $view = $_GET['table']; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Athome Scraper</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-slider.css">
    <link rel="stylesheet" type="text/css" href="assets/css/main.css?v=1.9">
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
    <div class="row filter-row">
        <div class="col-lg-2">
            City:
            <select id="f-city" class="form-control">
            </select>
        </div>
        <div class="col-lg-1">
            Commune:
            <select id="f-commune" class="form-control">
            </select>
        </div>
        <div class="col-lg-1">
            Region:
            <select id="f-region" class="form-control">
            </select>
        </div>
        <div class="col-lg-1">
            Cat Type:
            <select id="f-cat-type" class="form-control">
                <option value="any">Any</option>
                <option value="Apartment">Apartment</option>
                <option value="House">House</option>
            </select>
        </div>
        <div class="col-lg-1">
            Type:
            <select id="f-type" class="form-control">
            </select>
        </div>
        <div class="col-lg-2">
            Property:
            <select id="f-property" class="form-control">
            </select>
        </div>
        <div class="col-lg-1">
            Garage:
            <select id="f-garage" class="form-control">
                <option value="any">Any</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>
        </div>
        <div class="col-lg-1">
            Terrace:
            <select id="f-terrace" class="form-control">
                <option value="any">Any</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>
        </div>
        <div class="col-lg-1">
            Garden:
            <select id="f-garden" class="form-control">
                <option value="any">Any</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>
        </div>
        <div class="col-lg-1">
            <br>
            <button class="btn btn-primary" id="filter-btn"><i class="fa fa-search"></i></button>
        </div>
    </div>
  <div class="row">
    <div class="col-lg-1">
      <span class="btn btn-primary full-width">Green Cities <input type="checkbox" class="ch-lg" id="f-g-cities"></span>
    </div>
    <div class="col-lg-1">
      <span class="btn btn-primary full-width">Tag Out <input type="checkbox" class="ch-lg" id="f-tag-out"></span>
    </div>
    <div class="col-lg-3 price-range-container">
      <input type="text" class="span2 full-width" id="f-price-min-max" value="" data-slider-min="10" data-slider-max="1000000" data-slider-step="5" data-slider-value="[100000,500000]"/>
      <span class="pull-left min-value">100000</span>
      <span class="pull-right max-value">500000</span>
    </div>
    <div class="col-lg-3 perc-range-container">
      <input type="text" class="span2 full-width" id="f-perc-min-max" value="" data-slider-min="-100" data-slider-max="0" data-slider-step="1" data-slider-value="[-60,-10]"/>
      <span class="pull-left min-perc-value">-60%</span>
      <span class="pull-right max-perc-value">-10%</span>
    </div>

    <div class="col-lg-1">
      <button class="btn btn-primary full-width" id="best-price">BEST PRICE</button>
    </div>
    <div class="col-lg-1">
      <button class="btn btn-primary full-width" id="two-rooms">2 ROOMS</button>
    </div>
    <div class="col-lg-1">
      <button class="btn btn-primary full-width" id="reset">Reset All</button>
    </div>
  </div>
    <div class="row table-row">
        <div class="col-md-1">
            <button class="btn btn-primary pull-left" id="exportBtn"><i class="fa fa-file-excel-o"></i> Export</button>
        </div>
        <div class="col-lg-12 spacer">
            <table class="table table-bordered table-responsive-lg table-hover">
                <thead class="thead-dark">
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Comment</th>
                    <th scope="col" class="sort" data-action="ref_id" data-y="ASC">Ref ID <i
                            class="sort-arrow fa fa-caret-down"></i></th>
                    <th scope="col">Title</th>
                    <th scope="col" class="sort" data-action="city" data-y="ASC">City <i
                            class="sort-arrow fa fa-caret-down"></i></th>
                    <th scope="col" class="sort" data-action="commune" data-y="ASC">Commune <i
                            class="sort-arrow fa fa-caret-down"></i></th>
                    <th scope="col">Region</th>
                    <th scope="col">Cat Type</th>
                    <th scope="col">Type</th>
                    <th scope="col">Property</th>
                    <th scope="col" class="sort" data-action="size" data-y="ASC">Size sq<sup>2</sup> <i
                            class="sort-arrow fa fa-caret-down"></i></th>
                    <th scope="col" class="sort" data-action="price" data-y="ASC">Price <i
                            class="sort-arrow fa fa-caret-down"></i></th>
                    <th scope="col">P/S</th>
                    <th scope="col">P/S B</th>
                    <th scope="col">No. Bedrooms</th>
                    <th scope="col">No. Bathrooms</th>
                    <th scope="col">Garage</th>
                    <th scope="col">Terrace</th>
                    <th scope="col">Garden</th>
                    <th scope="col">Period</th>
                    <th scope="col"><i class="fa fa-globe"></i></th>
                    <th scope="col"><i class="fa fa-line-chart"></i></th>
                </tr>
                </thead>
                <tbody id="infoTbl">

                </tbody>
            </table>
        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="chartModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chartModalTitle">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="chart-loader loader"></div>
                            <div id="chartdiv" class="display-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<script type="text/javascript" src="assets/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-slider.js"></script>
<script type="text/javascript">
  $(document).ready(function () {
    $('#reset').click(function(){
      $('#f-city').val('any').trigger('click');
      $('#f-commune').val('any').trigger('click');
      $('#f-region').val('any').trigger('click');
      $('#f-cat-type').val('any').trigger('click');
      $('#f-type').val('any').trigger('click');
      $('#f-property').val('any').trigger('click');
      $('#f-garage').val('any').trigger('click');
      $('#f-terrace').val('any').trigger('click');
      $('#f-garden').val('any').trigger('click');
      $('#f-g-cities').prop('checked', false);
      $('#f-tag-out').prop('checked', false);


      $('.price-range-container').empty().html('<input type="text" class="span2 full-width" id="f-price-min-max" value="" data-slider-min="10" data-slider-max="1000000" data-slider-step="5" data-slider-value="[100000,500000]"/>'
        +'<span class="pull-left min-value">100000</span>'
        +'<span class="pull-right max-value">500000</span>');

      $('.perc-range-container').empty().html('<input type="text" class="span2 full-width" id="f-perc-min-max" value="" data-slider-min="-100" data-slider-max="0" data-slider-step="1" data-slider-value="[-60,-10]"/>'
        +'<span class="pull-left min-perc-value">-60%</span>'
        +'<span class="pull-right max-perc-value">-10%</span>');

      $('#f-price-min-max').slider({});
      $('#f-perc-min-max').slider({});
    });
    $('#f-price-min-max').slider({});

    $('#f-price-min-max').on('change', function(){
       $rangeValue = $(this).val();
       $range = $rangeValue.split(',');
       $('.min-value').text($range[0]);
       $('.max-value').text($range[1]);
    });

    $('#f-perc-min-max').slider({});

    $('#f-perc-min-max').on('change', function(){
      $rangeValue = $(this).val();
      $range = $rangeValue.split(',');
      $('.min-perc-value').text($range[0]+'%');
      $('.max-perc-value').text($range[1]+'%');
    });

    $chartData = [{date: '2018-08-28', price:4000}, {date:'2018-08-29', price:5000}];
    $chart = AmCharts.makeChart("chartdiv", {
      "type": "serial",
      "theme": "light",
      "marginRight": 80,
      "autoMarginOffset": 20,
      "marginTop": 7,
      "dataProvider": $chartData,
      "valueAxes": [{
        "axisAlpha": 0.2,
        "dashLength": 1,
        "position": "left"
      }],
      "mouseWheelZoomEnabled": true,
      "graphs": [{
        "id": "g1",
        "balloonText": "[[value]]",
        "bullet": "round",
        "bulletBorderAlpha": 1,
        "bulletColor": "#FFFFFF",
        "bulletSize": 5,
        "hideBulletsCount": 50,
        "title": "red line",
        "valueField": "price",
        "useLineColorForBulletBorder": true,
        "balloon": {
          "drop": true
        }
      }],
      "chartScrollbar": {
        "autoGridCount": true,
        "graph": "g1",
        "scrollbarHeight": 40
      },
      "chartCursor": {
        "limitToGraph": "g1"
      },
      "categoryField": "date",
      "categoryAxis": {
        "parseDates": true,
        "axisColor": "#DADADA",
        "dashLength": 1,
        "minorGridEnabled": true
      },
      "export": {
        "enabled": true
      }
    });

    $chart.addListener("rendered", zoomChart);
    zoomChart($chart, $chartData);

    // this method is called when chart is first inited as we listen for "rendered" event
    function zoomChart($chart, $chartData) {
      // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
      $chart.zoomToIndexes($chartData.length - 40, $chartData.length - 1);
    }


    // generate some random data, quite different range




    var sort = 'price';
    var y = 'ASC';

    getOptions().then(function () {
      getTable();
    })


    $('#exportBtn').click(function () {
      $city = $('#f-city').val();
      $commune = $('#f-commune').val();
      $region = $('#f-region').val();
      $catType = $('#f-cat-type').val();
      $type = $('#f-type').val();
      $property = $('#f-property').val();
      $garage = $('#f-garage').val();
      $terrace = $('#f-terrace').val();
      $garden = $('#f-garden').val();
      $greenCities = $('#f-g-cities').is(':checked');
      $tagOut = $('#f-tag-out').is(':checked');
      $priceRange = $('#f-price-min-max').val();
      $percRange = $('#f-perc-min-max').val();
      location.href = 'export.php?city=' + $city + '&commune=' + $commune + '&region=' + $region + '&type=' + $type + '&is_new_property=' + $property + '&garage=' + $garage + '&terrace=' + $terrace + '&garden=' + $garden + '&category='+$catType+'&greenCities='+$greenCities+'&tagOut='+$tagOut+'&priceRange='+$priceRange+'&percRange='+$percRange+'&table=<?=$view?>';
    });

    $('#filter-btn').click(function () {
      $city = $('#f-city').val();
      $commune = $('#f-commune').val();
      $region = $('#f-region').val();
      $catType = $('#f-cat-type').val();
      $type = $('#f-type').val();
      $property = $('#f-property').val();
      $garage = $('#f-garage').val();
      $terrace = $('#f-terrace').val();
      $garden = $('#f-garden').val();
      $greenCities = $('#f-g-cities').is(':checked');
      $tagOut = $('#f-tag-out').is(':checked');
      $priceRange = $('#f-price-min-max').val();
      $percRange = $('#f-perc-min-max').val();
      getTable(sort, y, $city, $commune, $region, $type, $property, $garage, $terrace, $garden, $catType, $greenCities, $tagOut, $priceRange, $percRange);
    });


    $('.sort').click(function () {
      $s = $(this);
      $y = $s.attr('data-y');
      $action = $s.attr('data-action');
      sort = $action;
      if ($y == 'DESC') {
        y = 'ASC';
        $s.attr('data-y', 'ASC');
        $s.find('.sort-arrow').removeClass('fa-caret-up').addClass('fa-caret-down');
      } else {
        y = 'DESC';
        $s.attr('data-y', 'DESC');
        $s.find('.sort-arrow').removeClass('fa-caret-down').addClass('fa-caret-up');
      }

      $city = $('#f-city').val();
      $commune = $('#f-commune').val();
      $region = $('#f-region').val();
      $catType = $('#f-cat-type').val();
      $type = $('#f-type').val();
      $property = $('#f-property').val();
      $garage = $('#f-garage').val();
      $terrace = $('#f-terrace').val();
      $garden = $('#f-garden').val();
      $greenCities = $('#f-g-cities').is(':checked');
      $tagOut = $('#f-tag-out').is(':checked');
      $priceRange = $('#f-price-min-max').val();
      $percRange = $('#f-perc-min-max').val();
      getTable($action, $y, $city, $commune, $region, $type, $property, $garage, $terrace, $garden, $catType, $greenCities, $tagOut, $priceRange, $percRange);

    });

    function getOptions() {
      return $.ajax({
        url: 'Controller/api.php?action=get-options',
        type: 'get',
        dataType: 'json',
        success: function (r) {
          $('#f-city').append($("<option />").val('any').text('any'));
          $('#f-commune').append($("<option />").val('any').text('any'));
          $('#f-region').append($("<option />").val('any').text('any'));
          $('#f-type').append($("<option />").val('any').text('any'));
          $('#f-property').append($("<option />").val('any').text('any'));
          $.each(r.city, function (i) {
            $('#f-city').append($("<option />").val(r.city[i]).text(r.city[i]));
          });
          $.each(r.commune, function (i) {
            $('#f-commune').append($("<option />").val(r.commune[i]).text(r.commune[i]));
          });
          $.each(r.region, function (i) {
            $('#f-region').append($("<option />").val(r.region[i]).text(r.region[i]));
          });
          $.each(r.type, function (i) {
            $('#f-type').append($("<option />").val(r.type[i]).text(r.type[i]));
          });
          $.each(r.property, function (i) {
            $('#f-property').append($("<option />").val(r.property[i]).text(r.property[i]));
          });
        }
      });
    }

    function getTable($sort = 'price', $y = 'DESC', $city = 'any', $commune = 'any', $region = 'any', $type = 'any', $property = 'any', $garage = 'any', $terrace = 'any', $garden = 'any', $category = 'any', $greenCities = false, $tagOut = false, $priceRange = '10000,500000', $percRange = '-60,-10') {
      $('#infoTbl').html('<tr><td colspan="22"><div class="loader"></div></td></tr>');
      $.ajax({
        url: 'Controller/api.php?action=get-info&sort=' + $sort + '&y=' + $y + '&city=' + $city + '&commune=' + $commune + '&region=' + $region + '&type=' + $type + '&is_new_property=' + $property + '&garage=' + $garage + '&terrace=' + $terrace + '&garden=' + $garden + '&category='+$category+'&greenCities='+$greenCities+'&tagOut='+$tagOut+'&priceRange='+$priceRange+'&percRange='+$percRange+'&table=<?=$view?>',
        type: 'get',
        dataType: 'json',
        success: function (r) {
          $tbl = '';
          if(r.length == 0){
            $tbl = '<tr class="text-center"><td colspan="22">No data available...</td></tr>'
          }
          $.each(r, function (index, key) {
            $pricePerSqM = (key.size != 0 ? (key.price / key.size) : 0);
            $psb = (key.average.averageSize != 0 ? (key.average.averagePrice / key.average.averageSize) : 0);
            $size = parseFloat(key.size);
            $price = parseFloat(key.price);

            if (key.flagged == 1) {
              $trClass = 'table-gray';
              $checked = 'checked';
            } else {
              if(key.city_found == 1){
                $trClass = 'table-success';
              }else{
                $trClass = '';
              }
              $checked = '';
            }

            if(key.is_new == 1){
              $newItem = '<i class="fa fa-star text-warning"></i>';
            }else{
              $newItem = '';
            }

            $tbl += '<tr class="' + $trClass + '">'
              + '<td class="cell-select"><input type="checkbox" style="width: 20px; height: 20px;" class="select" data-id="' + key.id + '" ' + $checked + '><br>'+$newItem+'</td>'
              + '<td><div class="commentBox" data-id="' + key.id + '" contenteditable="true"> ' + (key.comment != '' ? key.comment : '<i class="add-comment">add comment...</i>') + '</div></td>'
              + '<td>' + key.ref_id + '</td>'
              + '<td>' + key.title + '</td>'
              + '<td>' + key.city + '</td>'
              + '<td>' + key.commune + '</td>'
              + '<td>' + key.region + '</td>'
              + '<td>' + key.category + '</td>'
              + '<td>' + key.type + '</td>'
              + '<td>' + key.is_new_property + '</td>'
              + '<td>' + $size.toFixed() + '</td>'
              + '<td>' + $price.toFixed() + '</td>'
              + '<td>' + $pricePerSqM.toFixed() + '</td>'
              + '<td>' + $psb.toFixed() + '</td>'
              + '<td>' + key.no_of_bedrooms + '</td>'
              + '<td>' + key.no_of_bathrooms + '</td>'
              + '<td>' + key.garage + '</td>'
              + '<td>' + key.terrace + '</td>'
              + '<td>' + key.garden + '</td>'
              + '<td>' + key.date_created + '</td>'
              + '<td><a target="_blank" href="' + key.url + '"><i class="fa fa-globe"></i></a></td>'
              + '<td><span title="price history" class="render-chart" data-ref-id="' + key.ref_id + '" data-title="' + key.title + '" data-id="' + key.id + '"><i class="fa fa-line-chart" ></i></span></td>'
            '</tr>';
          });

          $('#infoTbl').html($tbl);


          $('.commentBox').keyup(function() {
            $id = $(this).attr('data-id');
            $comment = $(this).text();
            if($comment == ' add comment...' || $comment == 'add comment...'){
              $comment = '';
            }
            $.ajax({
              url: 'Controller/api.php?action=add-comment',
              type: 'post',
              dataType: 'json',
              success: function (r) {

              },
              data: {param: JSON.stringify({id: $id, comment: $comment})}
            });
          });

          $('.commentBox').on('click', function(){
            $comment = $(this).text();
            console.log($comment);
            if($comment == ' add comment...' || $comment == 'add comment...'){
              $(this).html('');
            }
          });

          $('.commentBox').focusout(function(){
            $comment = $(this).text();
            console.log($comment);
            if($comment == '' || $comment == ' '){
              $(this).html('<i class="add-comment">add comment...</i>');
            }
          });

          $('.render-chart').unbind().on('click', function(){

            $('#chartdiv').addClass('display-none');
            $('.chart-loader').removeClass('display-none');
            $('#chartModal').modal('show');
            $id = $(this).attr('data-id');
            $refId = $(this).attr('data-ref-id');
            $title = $(this).attr('data-title');
            console.log($id);
            $.ajax({
              url: 'Controller/api.php?action=price-history',
              type: 'post',
              dataType: 'json',
              success: function (r) {
                $('#chartModalTitle').html($title+' <small>Ref '+$refId+'</small>');
                $chartData = r;
                $chart.dataProvider = $chartData;
                $chart.validateData();

                $('.chart-loader').addClass('display-none');
                $('#chartdiv').removeClass('display-none');

              },
              data: {param: JSON.stringify({id: $id})}
            });
          });

          $('.select').click(function () {
            console.log($(this).is(':checked'));
            $tr = $(this).parent().parent();
            $infoId = $(this).attr('data-id');
            if ($(this).is(':checked') == true) {
              $flag = 1;
              $tr.addClass('table-gray');
            } else {
              $flag = 0;
              $tr.removeClass('table-gray');
            }

            $.ajax({
              url: 'Controller/api.php?action=tag',
              type: 'post',
              dataType: 'json',
              success: function (r) {
                console.log(r);
              },
              data: {param: JSON.stringify({id: $infoId, flag: $flag})}
            });
          });


        }
      });
    }


  });
</script>
<!-- Chart code -->

</body>

</html>