							
							<script type="text/javascript">
							<!--
								$(document).ready(function() {
									
									//console.log(CMSRootPath);
									
									$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'plugins/basics/eGallery.css?d=201912031600" type="text/css" />');	
									
									// DISABLE CHILDREN CLICK //
									$('.content .block[class^="block gallery_"] .img *').on('click', function(e) {
										e.preventDefault();
										e.stopPropagation();
										
										$(this).closest('.img').click();
									});

									// CREATE GALLERY ON CLICK //
									$('.content .block[class^="block gallery_"] .img').click(function(e) {		
										var tGallery = $(this).closest('.block');
										var tIndex = $(this).index();
										console.log('tIndex '+tIndex);
										myEGallery = new eGallery(tGallery, tIndex);
									});
									
									// INTERACTIVE GALLERY //
									$('body').on('click', '.egallery_container', function() {
										console.log('egallery_container click');
										myEGallery.close();
									});
									$('body').on('click', '.egallery, .navigator_container', function(e) {
										e.stopPropagation();
									});
									$('body').on('click', '.egallery_container .navigator_container>div', function(e) {
										
										var tIndex = 0;
										if (e.currentTarget.className=='previous') {
											tIndex = myEGallery.current-1
										} else {
											tIndex = myEGallery.current+1;
										}
												
										if (tIndex<0) { tIndex = myEGallery.nb_images-1; } else 
										if (tIndex==myEGallery.nb_images) { tIndex = 0; }
												
										myEGallery.navigateTo(tIndex);
										
									});
									
									$(document).on('keyup', function(event) {
										if (event.which == 37) {
											$('.egallery_container .navigator_container .previous').click();
										} else if (event.which == 39) {
											$('.egallery_container .navigator_container .next').click();
										}
									});
								});

								var myEGallery;
								function eGallery(tGallery, tIndex) {

									if (typeof(tIndex)==='undefined') {
										tIndex = 0
									}

									this.current = tIndex;
									this.gallery = tGallery;
									this.image = $(this.gallery).find('img:eq('+this.current+')');
									this.nb_images = $(this.gallery).find('img').length;
									
									var tDOMGallery = '<div class="egallery"><div class="img center img_center"><img src="'+this.image.attr('src')+'" width="'+this.image.attr('width')+'" height="'+this.image.attr('height')+'" alt="'+this.image.attr('alt')+'" title="'+this.image.attr('title')+'" /></div></div>';
									var tDOMNavigator = '<div class="navigator_container"><div class="previous">previous</div><div class="next">next</div></div>';
									$('body').append('<div class="egallery_container"><div class="close">X</div>'+tDOMNavigator+tDOMGallery+'</div>');
									setTimeout(function() { $('.egallery_container').css('opacity','1.0'); }, 50);
									
									$('.egallery img').on('load', function() {
										//console.log(this.naturalWidth+' '+this.naturalHeight);
										$(this).attr('width', this.naturalWidth);
										$(this).attr('height', this.naturalHeight);
										
										var tWindowWidth = $(window).width();
										var tWindowHeight = $(window).height();
										var tGalleryRatio = tWindowWidth/tWindowHeight;
										var tImgRatio = this.naturalWidth / this.naturalHeight;
										
										//console.log(tGalleryRatio+' '+tImgRatio+' '+$(window).height()+' '+$(window).width());
										
										var tGalleryMaxHeight = parseFloat($('.egallery').css('max-height'));
										if (parseFloat(tGalleryMaxHeight)<=100) {
											tGalleryMaxHeight = tGalleryMaxHeight * tWindowHeight / 100;
										}
										var tGalleryMaxWidth = parseFloat($('.egallery').css('max-width'));
										if (parseFloat(tGalleryMaxWidth)<=100) {
											tGalleryMaxWidth = tGalleryMaxWidth * tWindowWidth / 100;
										}
										
										console.log(tGalleryMaxWidth+' '+tGalleryMaxHeight);
										
										if (tGalleryMaxWidth>=this.naturalWidth && tGalleryMaxHeight>=this.naturalHeight) {
											// KEEP NATURAL SIZES //
											$('.egallery .img img').css({'width':this.naturalWidth+'px', 'height':this.naturalHeight+'px'});
										} else if (tGalleryRatio>tImgRatio) {
											// ADAPT TO HEIGHT //
											
											var tEGalleryRatio;
											if (parseFloat(tGalleryMaxHeight)<=100) {
												tEGalleryRatio = parseFloat(tGalleryMaxHeight)/100;
											} else {
												tEGalleryRatio = tGalleryMaxHeight/tWindowHeight;
											}	
											var tMaxHeight = $(window).height()*tEGalleryRatio;
											var tWidth = tMaxHeight * tImgRatio;
											
											//console.log(tWidth+' '+tMaxHeight+' '+tGalleryMaxHeight+' '+tEGalleryRatio);
											
											$('.egallery .img img').css({'width':tWidth+'px', 'height':tMaxHeight+'px'});
										} else {
											
											// ADAPT TO WIDTH //
											var tEGalleryRatio;
											if (parseFloat(tGalleryMaxWidth)<=100) {
												tEGalleryRatio = parseFloat(tGalleryMaxWidth)/100;
											} else {
												tEGalleryRatio = tGalleryMaxWidth/tWindowWidth;
											}	
											var tMaxWidth = $(window).width()*tEGalleryRatio;
											var tHeight = tMaxWidth / tImgRatio;
											
											//console.log(tMaxWidth+' '+tHeight+' '+tGalleryMaxWidth+' '+tEGalleryRatio+' '+$('.egallery').css('max-width')+' '+parseFloat($('.egallery').css('max-width')));
											
											$('.egallery .img img').css({'width':tMaxWidth+'px', 'height':tHeight+'px'});
										}
										
										$('.egallery .img img').css({'opacity':'1.0'});
										
									});
												
									this.navigateTo = function(tIndex) {
										
										$('.egallery .img img').css({'opacity':'0.0'});
										
										this.current = tIndex;
										this.image = $(this.gallery).find('img:eq('+this.current+')');
												
										//console.log($(this.image).length+' '+$(this.image).attr('src'));
										var tImgSrc = $(this.image).attr('src');
										var tFileName = tImgSrc.substring(0, tImgSrc.lastIndexOf('.'));
										var tFileType = tImgSrc.substring(tFileName.length);
										
										tImgSrc = tFileName+'_full'+tFileType;
										//console.log(tImgSrc);
												
										setTimeout(function() { $('.egallery img').attr('src', tImgSrc).attr('alt', $(this.image).attr('alt')).attr('title', $(this.image).attr('title')); }, 500);
										
									}
									
									this.close = function() {
										$('.egallery_container').css('opacity','0.0');
										setTimeout(function() { $('.egallery_container').remove(); }, 1000);
									}
									
									this.navigateTo(this.current);

								}
							-->
							</script>
							