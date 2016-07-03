<script type="text/javascript">
  $(function(){
    $.ajax({
      type: "POST",
      url: 'mainpage.php',
      data: ({:"13"}),
      success: function(data) {
        alert(data);
      }
    });
  });
</script>
