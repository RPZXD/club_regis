<?php 
// Read configuration from JSON file
$config = json_decode(file_get_contents('config.json'), true);
$global = $config['global'];

require_once('header.php');

?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?php echo $global['nameschool']; ?></h1>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <section class="content">
      <div class="flex flex-col items-center justify-center min-h-[60vh] bg-white rounded-lg shadow-lg p-8 mx-auto max-w-2xl">
        <div class="text-6xl mb-4">🎓✨</div>
        <h2 class="text-3xl font-bold text-gray-800 mb-2 text-center">ระบบรับสมัครชุมนุม</h2>
        <p class="text-lg text-gray-600 mb-6 text-center">
          ยินดีต้อนรับสู่ระบบรับสมัครชุมนุม<?php echo $global['nameschool']; ?>!<br>
          สมัครเข้าร่วมกิจกรรมที่คุณสนใจได้ง่ายๆ เพียงไม่กี่ขั้นตอน<br>
          <span class="text-2xl">🤝🏫🎉</span>
        </p>
        <div class="mb-6">
          <img src="<?php echo $global['logoLink']; ?>" alt="<?php echo $global['nameschool']; ?> Logo" class="max-h-40">
        </div>
        <a href="login.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded transition">
          สมัครชุมนุมเลย!
        </a>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    <?php require_once('footer.php');?>
</div>
<!-- ./wrapper -->


<script>

</script>
<?php require_once('script.php');?>
</body>
</html>
