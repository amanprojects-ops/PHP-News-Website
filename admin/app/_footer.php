          <!-- Footer -->
          <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
              <div class="mb-2 mb-md-0">
                © 2019 - 
                <script>
                  document.write(new Date().getFullYear());
                </script>
                , Created By
                <a href="https://technicalaman.co.in/" target="_blank" class="footer-link fw-bolder">💖 Technical Aman 💕</a>
              </div>
              <div>
                <a href="" class="footer-link me-4" target="_blank">About Us</a>
                <a href="" target="_blank" class="footer-link me-4">Contact Us</a>

                <a href="" target="_blank" class="footer-link me-4">Privacy Policy</a>

                <a href="" target="_blank" class="footer-link me-4">Disclaimer</a>
              </div>
            </div>
          </footer>
          <!-- / Footer -->

          <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
          </div>
          <!-- / Layout page -->
          </div>

          <!-- Overlay -->
          <div class="layout-overlay layout-menu-toggle"></div>
          </div>
          <!-- / Layout wrapper -->

          <!-- <div class="buy-now">
            <a href="https://technicalweb.online/" target="_blank" class="btn btn-danger btn-buy-now">Technical Baba</a>
          </div> -->
          <?php include_once "_modalBox.php"; ?>
          <!-- Core JS -->
          <!-- build:js assets/vendor/js/core.js -->
          <script src="../assets/vendor/libs/jquery/jquery.js"></script>
          <script src="../assets/vendor/libs/popper/popper.js"></script>
          <script src="../assets/vendor/js/bootstrap.js"></script>
          <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

          <script src="../assets/vendor/js/menu.js"></script>
          <!-- endbuild -->

          <!-- Vendors JS -->
          <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

          <!-- Main JS -->
          <script src="../assets/js/main.js"></script>

          <!-- Page JS -->
          <script src="../assets/js/dashboards-analytics.js"></script>

          <!-- Place this tag in your head or just before your close body tag. -->
          <script async defer src="https://buttons.github.io/buttons.js"></script>
          
          <script>
            $(document).ready(function(){
            
              $(document).on('click','.postShow',function(){
                var postId = $(this).data('pid');
                var post = postId;

                $.ajax({
                  url : '../app/loading-post.php',
                  type: 'POST',
                  data : {postid : postId,set:post},
                  success: function(postDetails){
                   $('#lodaingPost').html(postDetails);
                  }
                });
              });

              $(document).on('click','.CategoryShow',function(){
                var categoryId = $(this).data('cid');
                var category = categoryId;

                $.ajax({
                  url : '../app/lodaing-category.php',
                  type: 'POST',
                  data : {categoryid : categoryId,set:category},
                  success: function(categoryDetails){
                   $('#lodaingCategory').html(categoryDetails);
                  }
                });
              });
              $(document).on('click','.userShow',function(){
                var userId = $(this).data('uid');

                $.ajax({
                  url : '../app/lodaing-user-data.php',
                  type: 'POST',
                  data : {userid : userId,setUser:userId},
                  success: function(userDetails){
                   $('#lodaingUser').html(userDetails);
                  }
                });
              });
              $(document).on('click','.messageShow',function(){
                var msgId = $(this).data('mid');

                $.ajax({
                  url : '../app/lodaing-message.php',
                  type: 'POST',
                  data : {msgid : msgId,setmsg:msgId},
                  success: function(msgDetails){
                   $('#lodaingMessage').html(msgDetails);
                  }
                });
              });

              
            });
          </script>

          </body>

          </html>