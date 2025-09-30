let btnId;
jQuery(document).ready(function(){
    jQuery('div').click(function(){
        btnId = jQuery(event.target).parent('a').parent('div.elementor-widget-container').parent('div.first-class-video-btn').attr('id');
    });
    
    jQuery('.grade-button-tabs').click(function(){
        jQuery('.grade-button-tabs').removeClass('selected');
        jQuery(this).addClass('selected');
        let panel = jQuery(this).attr('data-tab');
        jQuery('.grade-button-panel').css('display','none');
        jQuery('.grade-button-panel[data-panel='+panel+']').fadeIn(500);
    });
    
    jQuery('div').click(function(event){
        btnId = jQuery(event.target).closest('div.first-class-video-btn').attr('id');
    });

    
    jQuery(document).on( 'elementor/popup/show', ( event, id, instance ) => {
        if(id == 11927 || id == 7164){
         if(btnId == undefined || btnId == ""){
                let videoHtml = '<div style="width:100%;height:300px;display:flex;justify-content:center;align-items:center"><h3 style="font-weight:bold">فیلم مورد نظر پیدا نشد.</h3></div>';
                jQuery('#aparatHtml .elementor-widget-container').html(videoHtml);
            }else{
                let videoHtml = '<div style="width:100%" class="h_iframe-aparat_embed_framev2"><iframe style="width:100%;height:100%" src="https://www.aparat.com/video/video/embed/videohash/'+btnId+'/vt/frame" allowFullScreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe></div>';
                jQuery('#aparatHtml .elementor-widget-container').html(videoHtml);
            }
    }
    });
    
    const teacherCourseNames = document.querySelectorAll('.teacher-course-name');
    teacherCourseNames.forEach(teacherCourseName => {
       if (teacherCourseName.childNodes.length === 0 || teacherCourseName.textContent.includes("teacher-course-name") || teacherCourseName.textContent.includes("pack-course-teacher")) {
            jQuery(teacherCourseName).text('گروه اساتید');
       }
    });
    
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
    
    //jQuery('[data-filter-id=4500] .jet-radio-list-wrapper .jet-radio-list__row .jet-radio-list__item input[value=176]').click();
	jQuery('[data-filter-id=4500] .jet-radio-list-wrapper .jet-radio-list__row .jet-radio-list__item input[name=field]').change(function(){
        let val = jQuery('[data-filter-id=4500] .jet-radio-list-wrapper .jet-radio-list__row .jet-radio-list__item input[name=field]:checked').val();
        if(val == 180){
            jQuery('#sa_riazi .elementor-widget-container .elementor-heading-title').text('المپیاد');
        }else if(val == 179){
            jQuery('#sa_riazi .elementor-widget-container .elementor-heading-title').text('رشته ریاضی و فیزیک');
        }else if(val == 177){
            jQuery('#sa_riazi .elementor-widget-container .elementor-heading-title').text('رشته علوم انسانی');
        }else if(val == 176){
            jQuery('#sa_riazi .elementor-widget-container .elementor-heading-title').text('رشته علوم تجربی');
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

function isMobile() {
      const regex = /Mobi|Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i;
      return regex.test(navigator.userAgent);
    }
