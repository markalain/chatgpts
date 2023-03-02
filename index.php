<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

<style type="text/css">
  body,.container-fluid{
    padding: 0;
    margin: 0;
  }
  .header{
    background: #000;
    color:#fff;
    height:80px;
  }
  .chat-message{
    border-radius: 10px;
      padding: 5px 15px;
      margin: 10px 0;
      line-height: 1.5em;
  }
  .message-out{
    background: #ddd;
  }
  .message-in{
    background: #3e4343;
    color:#fff;
  }
  .input-box{
    width:100%;
    position: fixed;
    bottom:0;
    height:120px;
    padding:20px 0px;
    background: #000;
  }
  #chat-input{
    margin: 20px auto;
      resize: none;
      border: none;
      color: #fff;
      background-color: #3e4343;
      outline: none;
      padding: 15px;
      font-size: 1.1em;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      width: 75%;
  }
  #cursor{
    width: 5px;
      height: 20px;
      background-color: #fff;
      display: inline-block;
      animation: blink 1s infinite;
  }
  @keyframes blink {
      0% {
          opacity: 0;
      }
      50% {
          opacity: 1;
      }
      100% {
          opacity: 0;
      }
  }
</style>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/default.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="header d-flex align-items-center justify-content-center">
        <h1>ChatJPT</h1>
      </div>
    </div>
  </div>
</div>

<div class="container mt-3 chat-content" style="margin-bottom:150px;">
  <div class="chat-messages">
    
  </div>
</div>
<div class="input-box">
  <textarea id="chat-input" placeholder="Type Here"></textarea>
</div>

<script type="text/javascript">
  var chat_input = $("#chat-input");
  var chat_container = $(".chat-content");
  var context = [];


  $(document).on('keypress',function(e) {
      if(e.which == 13) {
        message();
        e.preventDefault();
      }
  });

  function appendMessage(flag,message,className){
    html = '<div class="'+className+' chat-message message-'+flag+'">'+message+'</div>';
    chat_container.append(html);
      $("html, body").animate({ scrollTop: $(document).height() }, 300);

  }

  function message(){
    query = chat_input.val();
    appendMessage('out',query,'');
    appendMessage('in','<div id="cursor"></div>','temp');

    chat_input.val('');
    chat_input.focus();

    postData = {
      'message':query,
      'context':JSON.stringify(context)
    };
    url = 'message.php';
    $.ajax({
      url:url,
      type:'post',
      data:postData,
      success:function(res){
        if(res.status=='success'){
          $(".chat-message.temp").remove();
          appendMessage('in',res.message);
          context.push([query, res.raw_message]);
          hljs.highlightAll();
        }
      }
    });
  }
</script>