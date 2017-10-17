<!DOCTYPE html>
<!-- saved from url=(0039)http://kimarotec.com/demo/htmlvtc/html/ -->
<?php 
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$user = JFactory::getUser();
?>
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
     
      <!-- /.website title -->
     
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	
	  <jdoc:include type="head" />
	      
      <!-- CSS Files -->
	  <?php 
		JHtml::_('stylesheet', 'bootstrap.min.css', array('relative' => true));
		
		JHtml::_('stylesheet', 'font-awesome.min.css', array('relative' => true));
		
		JHtml::_('stylesheet', 'css-index-yellow.css', array('relative' => true));
		JHtml::_('stylesheet', 'user.css', array('relative' => true));
	
		
	
		JHtml::_('script', 'jquery.js', array('version' => 'auto', 'relative' => true));
		JHtml::_('script', 'bootstrap.min.js', array('version' => 'auto', 'relative' => true));


	  ?>

     



<script type="text/javascript" src="https://cdn.pushassist.com/account/assets/psa-ycar.js" async></script> 

   </head>
   <body data-spy="scroll" data-target="#navbar-scroll" style="" class="<?php if ($user->id) echo "driver_active" ?>">
      <!-- /.preloader -->
      <div id="top"></div>
      <!-- /.parallax full screen background image -->

      <!-- NAVIGATION -->
    
       <?php 
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('balance'));
		$query->from($db->quoteName('#__uber_driver'));
		$query->where($db->quoteName('id') . ' = '. $user->id);
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$balancer = $db->loadResult();
	   ?>
	   <script>
			function domenu() {
				 var x = document.getElementById('navbar-scroll');
			x.classList.toggle("mystyle");
			}
	   </script>
            <nav class="navbar-wrapper navbar-default">
               <div class="container">
                  <div class="navbar-header">
                     <button type="button" class="navbar-toggle" onclick="domenu()" >
						 <span class="sr-only">Toggle navigation</span>
						 <span class="icon-bar"></span>
						 <span class="icon-bar"></span>
						 <span class="icon-bar"></span>
                     </button>
					
                     <a class="navbar-brand site-name" href="#top">
						
						<?php if (!$user->id) {?>YCar <?php } else {?> 
						<b><?php echo $user->name?></b>: <span style="color:#e60000"><?php echo number_format($balancer)?> vnđ</span></a>
						<?php }?>
                  </div>
                  <div id="navbar-scroll" class="collapse navbar-collapse navbar-backyard navbar-right">
					<jdoc:include type="modules" name="position-1" style="none" />
                    
				</div>
				  </div>
            </nav>
		 
     
		<div id="main">
			<div class="container">
				
			<jdoc:include type="modules" name="position-3" style="xhtml" />
					<jdoc:include type="message" />
					<jdoc:include type="component" />
					<jdoc:include type="modules" name="position-2" style="none" />
			</div>		
		</div>
		
      <!-- /.footer -->
      <footer id="footer">
        
         
         <div class="footer-bottum">
            <div id="copyright">
               <div class="container">
                  <div class="col-sm-4 col-sm-offset-4 text-center">
                     <!-- /.social links -->
					 <?php if (!$user->id) {?>
                    
					 <?php } else {?>
						<b>Hỗ trợ lái xe: <a href="tel:0917999941" style="color:#fff">0917 999 941</a></b>
					 <?php }?>
                     <div class="text-center wow fadeInUp animated" style="font-size: 14px; visibility: visible; animation-name: fadeInUp;">Powered by LTD VietNam 2017</div>
                     <a href="#" class="scrollToTop" style="display: none;"><i class="pe-7s-up-arrow pe-va"></i></a>
                  </div>
               </div>
            </div>
         </div>
      </footer>
      <!-- /.javascript files -->
      

     

   </body>
</html>