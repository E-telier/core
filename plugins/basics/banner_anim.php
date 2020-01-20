									
									<script type="text/javascript">
									<!--
										$(document).ready(function() {
											
											$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'plugins/basics/banner_anim.css?d=201911271042" type="text/css" />');	
											
											$('.banner').mouseenter(function() { animBannerEffect(this); }).each(function(i) {

												banner_anim[i]=0;
												$(this).append('<div class="effect"></div>');
											});

										});

										banner_anim=new Array(); 
										function animBannerEffect(tDOM) {
											var tIndex = $(tDOM).index('.banner');

											if (banner_anim[tIndex]==0) { 
												banner_anim[tIndex] = 1;
												$(tDOM).find('.effect').animate({left:$(tDOM).width()+'px'}, 500, 'linear', 
													function() { 
														banner_anim[tIndex] = 0;
														$('.effect').css({left:'-'+$(tDOM).width()+'px'}); 
													});
											}
										}
									-->
									</script>
									