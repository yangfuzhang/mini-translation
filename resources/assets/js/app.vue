<template>
  <el-container>
    <el-header>
        <el-row>
            <el-col :xs="{span:6, offset:0}" :md="{span:2, offset:2}" :lg="{span:2, offset:4}">
	            <div class="appname">
	                <a href="http://www.limepietech.com" target="_blank">酸橙派</a>
	            </div>
            </el-col>
	        <el-col :xs="{span:18, offset:0}" :md="{span:18, offset:0}" :lg="{span:14, offset:0}">
	        	<el-menu class="el-menu-demo" mode="horizontal" @select="handleSelect">
	        	    <el-menu-item index="2"><a href="#contact">联系我们</a></el-menu-item>
				    <el-menu-item index="1"><a href="#contact">服务介绍</a></el-menu-item>
				</el-menu>
	        </el-col>
        </el-row>
    </el-header>

    <el-main>
        <el-row>
	        <el-col :xs="{span:24, offset:0}" :md="{span:20, offset:2}" :lg="{span:16, offset:4}">
		        <div style="width:100%;text-align:center;" id="service">
		            <h2 style="font-size:32px;color:#555;font-weight:bold;">网站建设、小程序开发</h2>
		            <p id="slogan">互联网+的时代，让您的产品离用户更近</p>
		            <img src="http://www.limepietech.com/public/images/web.png"/>
		        </div>
	        </el-col>
        </el-row>

        <el-row>
		    <el-col :xs="{span:24, offset:0}" :md="{span:18, offset:5}" :lg="{span:14, offset:5}" style="margin-top:40px;">
		        <el-col :lg="{span:8, offset:3}">
		        	<el-card>
			            <img src="http://www.limepietech.com/public/images/website.png" class="image">
				        <div style="padding:14px;text-align:center;" id="contact">
				            <span style="font-size:16px;">网站建设</span>
					        <div class="bottom clearfix">
					          <el-button type="text" class="button">定制开发</el-button>
					        </div>
				        </div>
			        </el-card>
		        </el-col>

		        <el-col :lg="{span:8, offset:2}">
		        	<el-card>
			            <img src="http://www.limepietech.com/public/images/wechat.png" class="image">
				        <div style="padding:14px;">
				            <span style="font-size:16px;">小程序开发</span>
					        <div class="bottom clearfix">
					          <el-button type="text" class="button">极速开发</el-button>
					        </div>
				        </div>
			        </el-card>
		        </el-col>
		    </el-col>
		</el-row>
    </el-main>
    
    <el-footer>
        <el-col :xs="{span:24, offset:0}" :md="{span:20, offset:2}" :lg="{span:16, offset:4}">
        	©Copyright 深圳市酸橙派网络科技有限公司 <a href="http://beian.miit.gov.cn" target="_blank">粤ICP备16126593号</a>
        </el-col>
    </el-footer>
  </el-container>
</template>

<style>
  a, a:hover, a:active {
    color:#888;
    text-decoration:none;
  }
  .appname {
    font-size:18px;
  }
  .el-header {
    width:100%;
    height:60px;
    line-height:60px;
    background-color:#fff;
    color:#333;
    border-bottom:1px solid #d3e0e9;
  }
  .el-menu {
  	height:60px;
  	text-align:right !important;
  }
  .el-menu-item {
  	float:right !important;
  }
  #slogan {
  	margin-top:10px;
  	font-size:16px;
  	color:#999;
  }
  img {
  	max-width:100%;
  }
  .el-card {
  	margin-bottom:20px;
  }
  .el-card__body {
  	text-align:center;
  }
  .image {
  	width:140px;
  }
  .el-footer {
  	line-height:60px;
  	margin-top:50px;
  	text-align:center;
  }
  @media screen and (max-width:600px) {
    
  }
</style>

<script>
  export default {
    data() {
      return {
        activeIndex: '1'
      };
    },
    created() {
      
    },
    mounted() {

    },
    methods: {
      handleSelect: function() {
      	console.log('hello')
      }
    },
    computed: {
      
    }
  }
</script>