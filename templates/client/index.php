
<!doctype html>
<html lang="en">
<head>
  <title>home</title>
  <meta charset="utf-8">
  <meta name="format-detection" content="telephone=no"/>
  <script src="js/3ts2ksmwxvkrug480knifj2_jnm.js"></script>
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="css/grid.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/booking.css">
  <link rel="stylesheet" href="css/rd-mailform.css">

  <script src="js/jquery.js"></script>
  <script src="js/jquery-migrate-1.2.1.js"></script>


  <!--[if lt ie 9]>
  <html class="lt-ie9">
  <div style=' clear: both; text-align:center; position: relative;'>
    <a href="https://windows.microsoft.com/en-us/internet-explorer/..">
      <img src="images/ie8-panel/warning_bar_0000_us.jpg" border="0" height="42" width="820"
           alt="you are using an outdated browser. for a faster, safer browsing experience, upgrade for free today."/>
    </a>
  </div>
  <script src="js/html5shiv.js"></script>
  <![endif]-->

  <script src='js/device.min.js'></script>
</head>

<body>
<div class="page">
<input id="origin-input" class="controls"  type="text"
        placeholder="Nhập điểm đi">

    <input id="destination-input" class="controls" type="text"
        placeholder="Nhập điểm đến">

    <div id="mode-selector" class="controls" style="display:hidden">
      <input type="radio" name="type" id="changemode-walking" >
      <label for="changemode-walking">Walking</label>

      <input type="radio" name="type" id="changemode-transit">
      <label for="changemode-transit">Transit</label>

      <input type="radio" name="type" id="changemode-driving" checked="checked">
      <label for="changemode-driving">Driving</label>
    </div>
	 
  <!--========================================================
                            header
  =========================================================-->
  <header class="parallax" data-url="images/parallax1.jpg" data-mobile="true">
    
      <div class="container">
        
        <div class="brand wow fadeinleft">
          <h1 class="brand_name">
            <a href="./">taxi</a>
          </h1>          
        </div>
       
        <div class="row">
			<div class="grid_4">
				    <p>Yes We Go</p>
        <address>
           Đi đâu cũng rẻ
        </address>
			</div>
          <div class="grid_8 bg-primary" >
		  <h4>
              Đặt xe đường dài
            </h4>
		   <div id="map"></div>
            

     
				 
				<div class="text-center">
					<div class="mfcontrols btn-group">
                  <button class="btn" type="submit" id="testbutton">Báo giá</button>
                </div>
					<a href="#pricing">Quý khách đặt xe đưa-đón sân bay vui lòng xe giá tại đây</a>
				</div>
          </div>
        </div>
       
      </div>

  </header>
  
   
		<script>
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          mapTypeControl: false,
          center: {lat: 21.0227387, lng: 105.8194541},
          zoom: 13
        });

        new AutocompleteDirectionsHandler(map);
      }

       /**
        * @constructor
       */
      function AutocompleteDirectionsHandler(map) {
        this.map = map;
        this.originPlaceId = null;
        this.destinationPlaceId = null;
        this.travelMode = 'DRIVING';
        var originInput = document.getElementById('origin-input');
		
        var destinationInput = document.getElementById('destination-input');
        var modeSelector = document.getElementById('mode-selector');
        this.directionsService = new google.maps.DirectionsService;
        this.directionsDisplay = new google.maps.DirectionsRenderer;
        this.directionsDisplay.setMap(map);

        var originAutocomplete = new google.maps.places.Autocomplete(
            originInput, {placeIdOnly: true});
        var destinationAutocomplete = new google.maps.places.Autocomplete(
            destinationInput, {placeIdOnly: true});

        this.setupClickListener('changemode-walking', 'WALKING');
        this.setupClickListener('changemode-transit', 'TRANSIT');
        this.setupClickListener('changemode-driving', 'DRIVING');

        this.setupPlaceChangedListener(originAutocomplete, 'ORIG');
        this.setupPlaceChangedListener(destinationAutocomplete, 'DEST');

        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(originInput);
        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(destinationInput);
        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(modeSelector);
      }

      // Sets a listener on a radio button to change the filter type on Places
      // Autocomplete.
      AutocompleteDirectionsHandler.prototype.setupClickListener = function(id, mode) {
        var radioButton = document.getElementById(id);
        var me = this;
        radioButton.addEventListener('click', function() {
          me.travelMode = mode;
          me.route();
        });
      };

      AutocompleteDirectionsHandler.prototype.setupPlaceChangedListener = function(autocomplete, mode) {
        var me = this;
        autocomplete.bindTo('bounds', this.map);
        autocomplete.addListener('place_changed', function() {
          var place = autocomplete.getPlace();
          if (!place.place_id) {
            window.alert("Please select an option from the dropdown list.");
            return;
          }
          if (mode === 'ORIG') {
            me.originPlaceId = place.place_id;
          } else {
            me.destinationPlaceId = place.place_id;
          }
          me.route();
        });

      };

      AutocompleteDirectionsHandler.prototype.route = function() {
        if (!this.originPlaceId || !this.destinationPlaceId) {
          return;
        }
        var me = this;

        this.directionsService.route({
          origin: {'placeId': this.originPlaceId},
          destination: {'placeId': this.destinationPlaceId},
          travelMode: this.travelMode
        }, function(response, status) {
          if (status === 'OK') {
            me.directionsDisplay.setDirections(response);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
      };

    </script>
	  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAcrOcaHxKGtFfZh8gbuk3DMJ3XVh6ATVY&libraries=places&callback=initMap"
        async defer></script>
  <!--========================================================
                            content
  =========================================================-->
  <main>

    <section class="well bg-primary"> 
     
      <div class="container">        
        <div class="row wow fadeinleft">

          <div class="grid_6">
            <div class="box" data-equal-group='3'>
              <div class="box_aside fa-tachometer mg-add"></div>
                <div class="box_cnt box_cnt__no-flow">
                  <h4>
                    Nhanh chóng
                  </h4>
                  <p class="clr1">
                    Đảm bảo tài xe đón quý khách đúng giờ. Bồi hoàn 100% nếu tài xế đón trễ quá 15 phút.
                  </p>
                </div>
            </div>  
          </div>
          <div class="grid_6">
            <div class="box" data-equal-group='3'>
              <div class="box_aside fa-thumbs-o-up mg-add"></div>
                <div class="box_cnt box_cnt__no-flow">
                  <h4>
                    Giá tốt nhất
                  </h4>
                  <p class="clr1">
                    Nếu quý khách tìm được giá rẻ hơn trong cùng thời điểm và cung đường, chúng tôi sẽ giảm thêm cho quý khách 20.000vnđ.
                  </p>
                </div>
            </div>  
          </div>

        </div>  
        <div class="row wow fadeinright">  

          <div class="grid_6">
            <div class="box" data-equal-group='3'>
              <div class="box_aside fa-cab mg-add"></div>
                <div class="box_cnt box_cnt__no-flow">
                  <h4>
                    Xe đời mới hiện đại
                  </h4>
                  <p class="clr1">
                    Các xe của Yes We Go đều hoạt động sau năm 2014. Đảm bảo chất lượng tuyệt vời cho những chuyến đi của quý khách.
                  </p>
                </div>
            </div>  
          </div>
          <div class="grid_6">
            <div class="box" data-equal-group='3'>
              <div class="box_aside fa-suitcase mg-add"></div>
                <div class="box_cnt box_cnt__no-flow">
                  <h4>
                    Khoang hành lý rộng rãi
                  </h4>
                  <p class="clr1">
                    Xe 5 chỗ và 7 chỗ với khoang hành lý rộng rãi. Quý khách không còn phải lo lắng về việc mang nhiều hành lý
                  </p>
                </div>
            </div>  
          </div>
          
        </div>
      </div>

    </section> 

    <section class="well1 sets1"> 
      
      <div class="container">        

        <h3 class="center">Giới thiệu Yes We Go</h3>

        <div class="row">

          <div class="grid_4">
            <h5>
               15 năm kinh nghiệm       
            </h5>
            <p data-equal-group='1'>
               Yes We Go hoạt động từ tháng 2 năm 2003. Chúng tôi đã hoàn thành trên 60.000 chuyến đi cho khách hàng. Hiện tại Yes We Go có hơn 200 tài xế sẵn sàng phục vụ quý khách mọi lúc, mọi nơi
            </p>
          </div>
          <div class="grid_4">
            <h5>
                Hỗ trợ khách hàng 24/7    
            </h5>
            <p data-equal-group='1'>
              Bộ phận chăm sóc khách hàng của Yes We Go hoạt động 24/7 luôn sẵng sàng hỗ trợ quý khách. 
            </p>
          </div>
          <div class="grid_4">
            <h5>
                Tích lũy cho mỗi chuyến đi
            </h5>
            <p data-equal-group='1'>
               Yes We Go là đơn vị duy nhất cho phép khách hàng tích lũy điểm trên mỗi chuyến đi. Với mỗi 10km quý khách sẽ được tích lũy 1 điểm tương được với 10.000vnđ. 
            </p>
          </div>  

        </div>
      </div>

    </section>   

    
    <section class="well well_ins1 parallax parallax1 center" data-url="images/parallax2.jpg" data-mobile="true" data-speed="0.8">

        <div class="container">
          <h2>
            An toàn của quý khách là sứ mệnh của chúng tôi
          </h2>          
          <p>
            Yes We Go luôn coi sự an toàn của quý khách trên mỗi chuyến đi là điều quan trọng nhất. Lái xe của chúng tôi được đào tạo bài bản, đi đúng tốc độ, làn đường. Đặc biệt không uống rượu bia khi lái xe.
          </p>      
        </div> 

    </section>


    <section class="well2" id="pricing"> 
      
      <div class="container">        

        <h3 class="center">Giá xe đưa đón sân bay</h3>

        <div class="row">

          <div class="grid_12 wow fadeinleft">
				<table class="table table-bordered text-center pricing-table">
				   <thead>
					  <tr>
						 <th class="text-center">Loại xe</th>
						 <th class="text-center">HÀ NỘI – SÂN BAY NỘI BÀI</th>
						 <th class="text-center">SÂN BAY NỘI BÀI – HÀ NỘI</th>
						 <th class="text-center">2 CHIỀU SÂN BAY TỪ HÀ NỘI</th>
					  </tr>
				   </thead>
				   <tbody>
					 
					  <tr>
						 <td>Xe 5 chỗ rộng</td>
						 <td>180.000 - 230.000 vnđ </td>
						 <td>250.000 - 320.000 vnđ</td>
						 <td>500.000 vnđ</td>
					  </tr>
					  <tr>
						 <td>Xe 7 chỗ</td>
						 <td>200.000 - 250.000 vnđ</td>
						 <td>280.000 - 350.000 vnđ</td>
						 <td>550.000 vnđ</td>
					  </tr>
					 <tr>
						 <td>Xe 16 chỗ</td>
						 <td>450.000 vnđ</td>
						 <td>450.000 vnđ</td>
						 <td>800.000 vnđ</td>
					  </tr>
				   </tbody>
				</table>
				<div class="text-center">
					<button class="btn" type="submit" id="book_airport">Đặt xe</button>
				</div>	
          </div>
         

        </div>

      </div>  

    </section>  

    <section class="well3 bg-secondary sets2"> 
      
      <div class="container">        

        <div class="row">

          <div class="grid_4 wow fadeinleft">
            <blockquote class="box1">
              <div class="box1_aside">
                <img src="images/page-1_img1.jpg" alt="">
              </div>
              <div class="box1_cnt">
                  <p>
                    <q>
                      Tôi rất hài lòng với dịch vụ đưa đón sân bay của Yes We Go. Xe luôn tới đúng giờ. 
                    </q>
                  </p>
                  <span>Mr. Khanh</span>                
              </div>
            </blockquote>
          </div>  

          <div class="grid_4">
            <blockquote class="box1">
              <div class="box1_aside">
                <img src="images/page-1_img2.jpg" alt="">
              </div>
              <div class="box1_cnt">
                  <p>
                    <q>
                     Tài xế Yes We Go rất thân thiện. Họ luôn mở cửa và xách đồ cho chúng tôi.
                    </q>
                  </p>
                  <span>Mr. Eddy</span>                
              </div>
            </blockquote>
          </div>  

          <div class="grid_4">
            <blockquote class="box1 wow fadeinright">
              <div class="box1_aside">
                <img src="images/page-1_img3.jpg" alt="">
              </div>
              <div class="box1_cnt">
                  <p>
                    <q>
                      Tôi luôn tìm được giá tốt nhất cho chuyến đi của mình tại Yes We Go
                    </q>
                  </p>
                  <span>Ms. Oanh</span>                
              </div>
            </blockquote>
          </div>  


          </div>          

        </div>       

    </section>  


  </main>

  <!--========================================================
                            footer
  =========================================================-->
  <footer class="center">
    <div class="container">


      <div class="brand wow fadeinright">
        <h1 class="brand_name">
          <a href="./">Yes We Go</a>
        </h1>          
      </div>
      <p class="invite">call us <span>24/7</span></p>
      <address>
          <a href="callto:#">0903 28 7499</a>
      </address>

    
      
      <ul class="inline-list">
        <li><a href="#" class="fa fa-facebook">facebook</a></li>
        <li><a href="#" class="fa fa-twitter">twitter</a></li>
        <li><a href="#" class="fa fa-google-plus">google-plus</a></li>
        <li><a href="#" class="fa fa-youtube">youtube</a></li>
      </ul>

        

      <p class="rights">Yes We Go &#169; <span id="copyright-year"></span>. all rights reserved</p>
      <!-- {%footer_link} -->
    </div>

      


  </footer>

</div>

<script src="js/script.js"></script>

<script>
	
		

		
		$(document).ready(function(){
			$("#book_airport").click(function(){
				// Get the modal
					var modal = document.getElementById('bookingModal');

					

					// Get the <span> element that closes the modal
					var span = document.getElementById("close_airport");

					// When the user clicks the button, open the modal 
					
					modal.style.display = "block";
					

					// When the user clicks on <span> (x), close the modal
					span.onclick = function() {
						modal.style.display = "none";
					}

					// When the user clicks anywhere outside of the modal, close it
					window.onclick = function(event) {
						if (event.target == modal) {
							modal.style.display = "none";
						}
					}
			});
			$("#testbutton").click(function(){
				var origins = document.getElementById('origin-input').value;	
				var destinations = document.getElementById('destination-input').value;
				if (origins && destinations) {
					document.getElementById("start_point").value = origins;
					document.getElementById("end_point").value = destinations;
					var start = document.getElementById("start");
								start.innerHTML = origins;
					var end = document.getElementById("end");
								end.innerHTML = destinations;			
					 var service = new google.maps.DistanceMatrixService();
					 service.getDistanceMatrix({
						  origins: [origins],
						  destinations: [destinations],
						  travelMode: google.maps.TravelMode.DRIVING,
						  unitSystem: google.maps.UnitSystem.METRIC,
						  avoidHighways: false,
						  avoidTolls: false
						},
						function(response, status) {
						  var dvTest = document.getElementById("dvDistance");
						//  dvTest.innerHTML += "getDistanceMatrix's callback.<br />";

						  if (status == google.maps.DistanceMatrixStatus.OK && response.rows[0].elements[0].status != "ZERO_RESULTS") {
							for (var i = 0; i < response.rows.length; i++) {
							  for (var j = 0; j < response.rows[i].elements.length; j++) {
								var distance = response.rows[i].elements[j].distance.text;
								var duration = response.rows[i].elements[j].duration.text;
								var dvDistance = document.getElementById("distance");
								dvDistance.innerHTML +=  distance;
								var dvDuration = document.getElementById("time");
								dvDuration.innerHTML =  duration;
								document.getElementById("distance_km").value = response.rows[i].elements[j].distance.value;
								document.getElementById("time_h").value = response.rows[i].elements[j].duration.value;
								var dist_value = response.rows[i].elements[j].distance.value;
								dist_value = Math.round(dist_value/1000);
								var price_5; var price_7;
								if (dist_value < 50) {
									price_5_1 = dist_value*10000;
									price_5_2 = 2*dist_value*8000;
									price_7_1 = dist_value*13000;
									price_7_2 = 2*dist_value*9000;
								}else if (dist_value < 80){
									price_5_1 = dist_value*10000;
									price_5_2 = 2*dist_value*7000;
									price_7_1 = dist_value*12000;
									price_7_2 = 2*dist_value*8000;
								}else if (dist_value < 130){
									price_5_1 = dist_value*9000;
									price_5_2 = 2*dist_value*6000;
									price_7_1 = dist_value*10000;
									price_7_2 = 2*dist_value*7000;
								}else if (dist_value < 180){
									price_5_1 = dist_value*9000;
									price_5_2 = 2*dist_value*5500;
									price_7_1 = dist_value*10000;
									price_7_2 = 2*dist_value*6000;
								}else {
									price_5_1 = dist_value*8500;
									price_5_2 = 2*dist_value*5000;
									price_7_1 = dist_value*9000;
									price_7_2 = 2*dist_value*5500;
								}
								document.getElementById("5_seats_price").innerHTML = price_5_1;
								document.getElementById("5_2_seats_price").innerHTML = price_5_2;
								document.getElementById("7_seats_price").innerHTML = price_7_1;
								document.getElementById("7_2_seats_price").innerHTML = price_7_2;
								
							  }
							}
						  } else {
							dvTest = document.getElementById("dvDistance");
							dvTest.innerHTML += " For started, locations length = " + locations.length + "<br />";
						  }
						}
						);
					// Get the modal
					var modal = document.getElementById('myModal');

					// Get the button that opens the modal
					var btn = document.getElementById("myBtn");

					// Get the <span> element that closes the modal
					var span = document.getElementsByClassName("close")[0];

					// When the user clicks the button, open the modal 
					
					modal.style.display = "block";
					

					// When the user clicks on <span> (x), close the modal
					span.onclick = function() {
						modal.style.display = "none";
					}

					// When the user clicks anywhere outside of the modal, close it
					window.onclick = function(event) {
						if (event.target == modal) {
							modal.style.display = "none";
						}
					}
				}else {
					alert("Vui lòng nhập điểm đi và điểm đến");
				}	
			});
		});
	</script>
			<!-- Modal -->
		<div class="modal" id="myModal">
		
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Báo giá</h5>
				<span class="close">&times;</span>
			  </div>
			  <form action="xuly.php" method="post" id="ajaxform">
			  <div class="modal-body">
				<div class="row">
					 <div class="grid_6">
							<ul>
								<li>Điểm đi: <b><span id="start"></span></b></li>
								<li>Điểm đến: <b><span id="end"></span></b></li>
								<li>Quãng đường dự kiến: <b><span id="distance"></span></b></li>
								<li>Thời gian dự kiến: <b><span id="time"></span></b></li>
								<li>Giá tham khảo: <b><span style="color:red">Chưa bao gồm phí cầu đường</span></b></li>
								<input type="radio" name="car_type" value="1" checked/> Xe 5 chỗ 1 chiều: <span id="5_seats_price"></span><br/>
								<input type="radio" name="car_type" value="2"/> Xe 5 chỗ 2 chiều: <span id="5_2_seats_price"></span><br/>
								<input type="radio" name="car_type" value="3"/> Xe 7 chỗ 1 chiều: <span id="7_seats_price"></span><br/>
								<input type="radio" name="car_type" value="4"/> Xe 7 chỗ 2 chiều: <span id="7_2_seats_price"></span>
							</ul>
					 </div>
					  <div class="grid_6">
						<div id="field_available">
							<input type="text" name="name" placeholder="Tên khách hàng" required/>
							<input type="text" name="phone" placeholder="Số điện thoại" required/>
							<label>Ngày/giờ đón:</label>
							<input type="date" name="date"/>
							<input type="time" name="time"/>
							<label>Ghi chú</label>
							<textarea name="comment"></textarea>
							<button type="submit" class="btn btn-primary btn-booking">Đặt xe</button>
						</div>
						<div id="booking_message" style="display:none;">
							Chúng tôi đã nhận được thông tin đặt xe của quý khách. Chúng tôi sẽ liên lạc lại với quý khách trong thời gian sớm nhất!
						</div>	
					 </div>
				</div>
			
				<div id="booking">
					
						<input type="hidden" id="start_point" name="start_point" value=""/>	
						<input type="hidden" id="end_point" name="end_point" value=""/>	
						<input type="hidden" id="distance_km"name="distance_km" value=""/>	
						<input type="hidden" id="time_h" name="time_h" value=""/>	
						<input type="hidden" id="et_price" name="et_price" value=""/>	
						 
					
				</div>
				</form>
			  </div>
			
			</div>
		
		</div>
			<div class="modal" id="bookingModal">
		
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Đặt xe</h5>
				<span class="close" id="close_airport">&times;</span>
			  </div>
			  <form action="xuly.php" method="post" id="ajaxformairport">
			  <div class="modal-body">
				<div class="row">
					
							<div id="field_available2">
								<select name="is_airport">
									<option value="1">Đón tại sân bay</option>
									<option value="2">Tiễn đi sân bay</option>
								</select>
								<input type="text" name="location" placeholder="Địa điểm"/>
								
						
				
						
							<input type="text" name="name" placeholder="Tên khách hàng" required/>
							<input type="text" name="phone" placeholder="Số điện thoại" required/>
							<label>Ngày/giờ đón:</label>
							<input type="date" name="date"/>
							<input type="time" name="time"/>
							<select name="car_type">
								<option value="1"> Xe 5 chỗ 1 chiều</option>
								<option value="2"> Xe 5 chỗ 2 chiều</option>
								<option value="3"> Xe 7 chỗ 1 chiều</option>
								<option value="4"> Xe 7 chỗ 2 chiều</option>
							</select>
						
							<label>Ghi chú</label>
							<textarea name="comment"></textarea>
							<button type="submit" class="btn btn-primary btn-booking">Đặt xe</button>
							</div>
						<div id="booking_message2" style="display:none;">
							Chúng tôi đã nhận được thông tin đặt xe của quý khách. Chúng tôi sẽ liên lạc lại với quý khách trong thời gian sớm nhất!
						</div>	
					 </div>
				</div>
			
				
				</form>
			  </div>
			
			</div>
		
		</div>
		<script>
			//callback handler for form submit
			$("#ajaxformairport").submit(function(e)
			{
				 var data = $('form#ajaxformairport').serialize();
					//su dung ham $.ajax()
					$.ajax({
					type : 'POST', //kiểu post
					url  : 'xuly.php', //gửi dữ liệu sang trang submit.php
					data : data,
					success :  function(data)
						   {                       
								if(data == 'false')
								{
									alert('Sai tên hoặc mật khẩu');
								}else{
									var field_available = document.getElementById('field_available2');
									var booking_message = document.getElementById('booking_message2');
									field_available.style.display = "none";
									booking_message.style.display = "block";
									
								}
						   }
					});
					return false;
			});
			$("#ajaxform").submit(function(e)
			{
				 var data = $('form#ajaxform').serialize();
					//su dung ham $.ajax()
					$.ajax({
					type : 'POST', //kiểu post
					url  : 'xuly.php', //gửi dữ liệu sang trang submit.php
					data : data,
					success :  function(data)
						   {                       
								if(data == 'false')
								{
									alert('Sai tên hoặc mật khẩu');
								}else{
									var field_available = document.getElementById('field_available');
									var booking_message = document.getElementById('booking_message');
									field_available.style.display = "none";
									booking_message.style.display = "block";
									
								}
						   }
					});
					return false;
			});
			 
		
		</script>
</body>
</html>