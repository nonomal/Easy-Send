
<div id="input">
    <form class="layui-form getbox">
         <div class="layui-form-item layui-anim layui-anim-upbit">
           <input type="text" name="key" lay-verify="get" pattern="/^[a-zA-Z0-9]{4}$/" autocomplete="off" placeholder="请输入4位提取码" id="get-input" value="<?php echo $_REQUEST['key']?>" data-anim="layui-anim-down" class="layui-input get "onKeypress="javascript:if(event.keyCode == 32)event.returnValue = false;" required>
           </div>
          <div class="info">
          <button type="submit" class="layui-btn btn layui-anim layui-anim-upbit layui-bg-blue" lay-submit="" lay-filter="getbtn" data-anim="layui-anim-down">立即提取</button>
          </div>
    </form>
</div>
<div id="result" class="layui-hide formbox">
    <div class="layui-form-item layui-form-text layui-anim layui-anim-upbit layui-hide" id="result-text">
      <textarea class="layui-textarea textarea" data-anim="layui-anim-down" id="result-value"></textarea>
    </div>
    <div class="layui-form-item layui-anim layui-anim-upbit layui-hide" id="result-file">
        <input type="text" value="" autocomplete="off" id="result-url" class="layui-input get" data-anim="layui-anim-down">
    </div>
    <div class="info layui-hide" id="result-download-btn">
        <a id="result-download" href="" target="_blank">
        <button type="button" class="layui-btn btn layui-anim layui-anim-upbit layui-bg-blue" data-anim="layui-anim-down">立即下载</button>
        </a>
    </div>
    <div class="info" id="result-info"></div>
</div>