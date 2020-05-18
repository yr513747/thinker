<div class="header-nav">
  <div class="inside">
    <p class="logo-name">{{$params['cfg_soft_enname']}}<span>{{$version}}</span></p>
    <ul class="schedule {{if condition="'index'==$Request.action"}}active{{/if}}">
      <li class="number">1</li>
      <li class="word">{{$steps[1]}}</li>
    </ul>
    <ul class="schedule {{if condition="'step2'==$Request.action"}}active{{/if}}">
      <li class="number">2</li>
      <li class="word">{{$steps[2]}}</li>
    </ul>
    <ul class="schedule {{if condition="'step3'==$Request.action"}}active{{/if}}">
      <li class="number">3</li>
      <li class="word">{{$steps[3]}}</li>
    </ul>
    <ul class="schedule {{if condition="'step4'==$Request.action"}}active{{/if}}">
      <li class="number">4</li>
      <li class="word">{{$steps[4]}}</li>
    </ul>
  </div>
</div>
