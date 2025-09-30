jQuery(document).ready(function(){
    /*jQuery('.success-story-tabitem').click(function(){
    	let desc = jQuery(this).children('.success-story-desc').children('.elementor-widget-container').children('p').text();
    	let name = jQuery(this).children('.succes-story-name').children('.elementor-widget-container').children('span').text();
    	jQuery('.success-story-panel').fadeOut();
    	let successTime = setTimeout(function(){
    	    jQuery('.success-story-panel .elementor-widget-heading .elementor-widget-container h4').text(name);
    	    jQuery('.success-story-panel .elementor-widget-text-editor .elementor-widget-container p').text(desc);
    	}, 500);
    	jQuery('.success-story-panel').fadeIn(1000);
    });*/
    
    //Link Hero parts to pages
    jQuery('.part').click(function(){
        let href = jQuery(this).attr('data-href');
        
        if(href != "" && href != undefined){
            window.open(href, '_blank');
        }
    });
    
    let faqCatArray = [65,59,60,58,57,56,62,64,63,61];
    for(let i=0;i<faqCatArray.length;i++){
       jQuery('[data-post-id='+faqCatArray[i]+'] a').removeAttr('href').css('color','#888');
       jQuery('[data-post-id='+faqCatArray[i]+'] img').css('filter','grayscale(100%)');
    }
    
    let platformTabCounter = 0;
    const maxTabs = 9;
    const intervalTime = 5000;
    
    const platformTabTimer = setInterval(() => {
        platformTabCounter = (platformTabCounter % maxTabs) + 1;
        updateActiveTab(platformTabCounter);
    }, intervalTime);

});
  
/*document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.navteacher a').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            const navbarHeight = document.querySelector('.navteacher').offsetHeight;
            const targetScrollPosition = targetSection.offsetTop - navbarHeight;
            window.scrollTo({
                top: targetScrollPosition,
                behavior: 'smooth'
            });
        });
    });
});*/
document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('.navteacher');
    const navbarHeight = navbar ? navbar.offsetHeight : 0;

    document.querySelectorAll('.navteacher a').forEach(anchor => {
        anchor.addEventListener('click', event => {
            event.preventDefault();
            const targetId = anchor.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            
            if (targetSection) {
                const targetScrollPosition = targetSection.offsetTop - navbarHeight;
                window.scrollTo({ top: targetScrollPosition, behavior: 'smooth' });
            }
        });
    });
});


function updateActiveTab(counter) {
    // فعال‌سازی آیتم‌های تب و محتوا
    jQuery('#platformTab .bdt-tab .bdt-tabs-item').removeClass('bdt-active');
    jQuery(`#platformTab .bdt-tab .bdt-tabs-item:nth-child(${counter})`).addClass('bdt-active');
    jQuery('#platformTab .bdt-switcher-item-content .bdt-tab-content-item').removeClass('bdt-active');
    jQuery(`#platformTab .bdt-switcher-item-content .bdt-tab-content-item:nth-child(${counter})`).addClass('bdt-active');
    
    // تنظیم موقعیت اسکرول نسبی
    const parent = jQuery('#platformTab .bdt-first-column');
    const child = jQuery(`#platformTab .bdt-tab .bdt-tabs-item:nth-child(${counter})`);

    if (child.length && parent.length) {
        const relativeTop = child.offset().top - parent.offset().top;
        parent.animate({ scrollTop: parent.scrollTop() + relativeTop }, 600);
    }
}
