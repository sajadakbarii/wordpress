let btnId;

function isMobile() {
    const regex = /Mobi|Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i;
    return regex.test(navigator.userAgent);
}

function getTransform(el) {
    var results = jQuery(el).css('-webkit-transform').match(/matrix(?:(3d)\(\d+(?:, \d+)*(?:, (\d+))(?:, (\d+))(?:, (\d+)), \d+\)|\(\d+(?:, \d+)*(?:, (\d+))(?:, (\d+))\))/)

    if(!results) return [0, 0, 0];
    if(results[1] == '3d') return results.slice(2,5);

    results.push(0);
    return results.slice(5, 8);
}

function getRandomInt(max) {
  return Math.floor(Math.random() * max);
}

jQuery(document).ready(function(){
    jQuery.fn.shuffle = function() {
        var allElems = this.get(),
        getRandom = function(max) {
        return Math.floor(Math.random() * max);
        },
        shuffled = jQuery.map(allElems, function(){
        var random = getRandom(allElems.length),
        randEl = jQuery(allElems[random]).clone(true)[0];
        allElems.splice(random, 1);
        return randEl;
        });
        this.each(function(i){
        jQuery(this).replaceWith(jQuery(shuffled[i]));
        });
    return jQuery(shuffled);
};

    /* coursetype-tabitems tab */
    jQuery('.coursetype-tabitems .jet-listing-grid__item').click(function(){
        let tabItemsId = jQuery(this).attr('data-post-id');
        let tabindex = jQuery(this).attr('tabindex');
        let tabItemsIndex = jQuery(this).attr('data-slick-index');
        let tabcenter = jQuery('.slick-center').attr('data-slick-index');
        let offsettabs = Math.abs(tabcenter - tabItemsIndex);
        let tabsmove = offsettabs*172;
        let getpos = getTransform('.slick-track');
        jQuery('.coursetype-tabpanel').hide();
        jQuery('.coursetype-tabpanel[data-panel="'+tabItemsId+'"]').fadeIn();
        if(tabindex == -1){
            jQuery('.jet-listing-grid__slider-icon.prev-arrow').click();
        }else{
            jQuery('.jet-listing-grid__slider-icon.next-arrow').click();
        }
    });
    
    
    jQuery('div').click(function(event){
        btnId = jQuery(event.target).closest('div.first-class-video-btn').attr('id');
    });

    
    jQuery(document).on( 'elementor/popup/show', ( event, id, instance ) => {
        if(id == 8928 || id == 10199){
         if(btnId == undefined || btnId == ""){
                let videoHtml = '<div style="width:100%;height:300px;display:flex;justify-content:center;align-items:center"><h3 style="font-weight:bold">فیلم مورد نظر پیدا نشد.</h3></div>';
                jQuery('#aparatHtml .elementor-widget-container').html(videoHtml);
            }else{
                let videoHtml = '<div style="width:100%" class="h_iframe-aparat_embed_frame"><iframe style="width:100%;height:100%" src="https://www.aparat.com/video/video/embed/videohash/'+btnId+'/vt/frame" allowFullScreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe></div>';

                jQuery('#aparatHtml .elementor-widget-container').html(videoHtml);
            }
    }
    });
    
    //our navigation for carousels
    /*jQuery('.crs-arrow').click(function(){
        //let bId = jQuery(this).attr('id');
        let bTarget = jQuery(this).attr('data-target');
        jQuery(bTarget).click();
    });*/
    
    //Random banner side of main slider
    /*jQuery('.rnd-banner-1').each(function() {
        let rndBannerId = jQuery(this).attr('data-id');
        let banner_count = jQuery('[data-id='+ rndBannerId +'] .swiper-wrapper').children().length;
        console.log(banner_count);
        if(banner_count > 1){
            console.log('yes');
            let banner_selected = getRandomInt(banner_count) + 1;
            console.log(banner_selected);
            const rndBanner1 = setTimeout(function(){
                jQuery('[data-id='+ rndBannerId +'] .swiper-wrapper .swiper-slide').removeClass('swiper-slide-active').css({"opacity":"0"});
                jQuery('[data-id='+ rndBannerId +'] .swiper-wrapper .swiper-slide:nth-child('+banner_selected+')').addClass('swiper-slide-active').css({"opacity":"1"});
            }, 1000);
        }
    });*/
    
    const elements = document.querySelectorAll('.isempty');
    elements.forEach(element => {
       if (element.childNodes.length === 0) {
        jQuery(element).closest('[data-parent="isemptyp"]').addClass('d-none');
       }else if(element.textContent === '0'){
           jQuery(element).closest('.price').text('رایگان').css('font-size','20px');
           jQuery('.taxtxt').hide();
       }
    });
    
    const elements2 = document.querySelectorAll('.isempty2');
    elements2.forEach(element2 => {
        let whatattr = element2.getAttribute('data-attr');
        let whatattrVal = element2.getAttribute(whatattr);
       if (whatattrVal.length === 0) {
        jQuery(element2).addClass('d-none');
       }
    });
    
    const teacherCourseNames = document.querySelectorAll('.teacher-course-name');
    teacherCourseNames.forEach(teacherCourseName => {
       if (teacherCourseName.childNodes.length === 0 || teacherCourseName.textContent.includes("teacher-course-name") || teacherCourseName.textContent.includes("pack-course-teacher")) {
            jQuery(teacherCourseName).text('گروه اساتید');
       }
    });
    
    if (jQuery(".randteachersort")[0]){
        var nums = [],
            ranNums = [];
            
        let j = jQuery('.randteachersort').children('li').length;
        for(let i=1;i<=j;i++){
           nums.push(i);
        }
        let max = nums.length, min=0;
        
        while (max--) {
            min = Math.floor(Math.random() * (max+1));
            ranNums.push(nums[min]);
            nums.splice(min,1);
        }
        let count = 0;
        jQuery('.randteachersort > li').each(function() {
            var li = jQuery(this);
            jQuery(this).css('order',ranNums[count]);
            count++;
        });
    }
    
    if(isMobile()){
        jQuery(".bdt-ep-fancy-tabs-item").click(function(){
            document.querySelector(".bdt-ep-fancy-tabs-content-wrap").scrollIntoView();
        });
    }
    
    /*clearInterval(sortCarouselTimer);
    var sortCarouselTimer = setInterval(function(){
        jQuery(".jet-listing-grid--3999 .jet-listing-grid__item").shuffle();
    }, 10000);*/
    
    // Check if the element with class 'jet-listing-not-found' exists
      if (jQuery('.jet-listing-not-found').length) {
        // Get the 4th-level parent of the found element
        let packpanel = jQuery('.jet-listing-not-found').parents().filter('#packpanel');
        let singlecoursepanel = jQuery('.jet-listing-not-found').parents().filter('#singlecoursepanel');
        // Check if the 4th-level parent has an ID
        if (packpanel.length) {
          // You can perform further actions, like hiding the parent if it has a specific ID
          packpanel.hide();
        }
        if (singlecoursepanel.length) {
          // You can perform further actions, like hiding the parent if it has a specific ID
          singlecoursepanel.hide();
        }
      }
});

