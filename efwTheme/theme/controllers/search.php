<?php
$data2 =  \efwEngine\app\search::searchUsers(\efwTheme\router::getParameters()[0]);
$result = $data2;
function shuffle_assoc(&$array) {
    $keys = array_keys($array);

    shuffle($keys);

    foreach($keys as $key) {
        $new[$key] = $array[$key];
    }

    $array = $new;

    return true;
}
shuffle($result);
$result = ["listItems" =>

         $result

];
echo json_encode($result);


/*

?>







{
"listItems": [
{"name":"Analytics Dashboard", "url":"dashboard-analytics.html","icon":"feather icon-home"},
{"name":"eCommerce Dashboard", "url":"dashboard-ecommerce.html","icon":"feather icon-home"},
{"name":"Email", "url":"app-email.html","icon":"feather icon-mail"},
{"name":"Chat", "url":"app-chat.html","icon":"feather icon-message-square"},
{"name":"Todo", "url":"app-todo.html","icon":"feather icon-check-square"},
{"name":"Calender", "url":"app-calender.html","icon":"feather icon-calendar"},
{"name":"Shop Ecommerce", "url":"app-ecommerce-shop.html","icon":"feather icon-shopping-cart"},
{"name":"Product Details", "url":"app-ecommerce-details.html","icon":"feather icon-circle"},
{"name":"Wish List", "url":"app-ecommerce-wishlist.html","icon":"feather icon-heart"},
{"name":"Checkout", "url":"app-ecommerce-checkout.html","icon":"feather icon-credit-card"},
{"name":"Data List - List View", "url":"data-list-view.html","icon":"feather icon-list"},
{"name":"Data List - Thumb View", "url":"data-thumb-view.html","icon":"feather icon-image"},
{"name":"Content - Grid", "url":"content-grid.html","icon":"feather icon-grid"},
{"name":"Content - Typography", "url":"content-typography.html","icon":"feather icon-type"},
{"name":"Content - Text Utilities", "url":"content-text-utilities.html","icon":"feather icon-type"},
{"name":"Content - Syntax Highlighter", "url":"content-syntax-highlighter.html","icon":"feather icon-hash"},
{"name":"Content - Helper Classes", "url":"content-helper-classes.html","icon":"feather icon-help-circle"},
{"name":"Colors", "url":"colors.html","icon":"feather icon-feather"},
{"name":"Feather Icon", "url":"icons-feather.html","icon":"feather icon-feather"},
{"name":"Font Awesome Icon", "url":"icons-font-awesome.html","icon":"feather icon-wind"},
{"name":"Card Basic", "url":"card-basic.html","icon":"feather icon-square"},
{"name":"Card Advance", "url":"card-advance.html","icon":"feather icon-tablet"},
{"name":"Card Statistics", "url":"card-statistics.html","icon":"feather icon-smartphone"},
{"name":"Card Analytics", "url":"card-analytics.html","icon":"feather icon-bar-chart-2"},
{"name":"Card Actions", "url":"card-actions.html","icon":"feather icon-airplay"},
{"name":"Table", "url":"table.html","icon":"feather icon-grid"},
{"name":"Datatable", "url":"table-datatable.html","icon":"feather icon-grid"},
{"name":"agGrid Table", "url":"table-ag-grid.html","icon":"feather icon-grid"},
{"name":"Alerts Component", "url":"component-alerts.html","icon":"feather icon-info"},
{"name":"Buttons Component", "url":"component-buttons-basic.html","icon":"feather icon-inbox"},
{"name":"Breadcrumbs Component", "url":"component-breadcrumbs.html","icon":"feather icon-more-horizontal"},
{"name":"Carousel Component", "url":"component-carousel.html","icon":"feather icon-map"},
{"name":"Collapse Component", "url":"component-collapse.html","icon":"feather icon-minimize"},
{"name":"Dropdowns Component", "url":"component-dropdowns.html","icon":"feather icon-inbox"},
{"name":"List Group Component", "url":"component-list-group.html","icon":"feather icon-layers"},
{"name":"Modals Component", "url":"component-modals.html","icon":"feather icon-maximize-2"},
{"name":"Pagination Component", "url":"component-pagination.html","icon":"feather icon-chevrons-right"},
{"name":"Navs Component", "url":"component-navs-component.html","icon":"feather icon-more-vertical"},
{"name":"Navbar Component", "url":"component-navbar.html","icon":"feather icon-more-horizontal"},
{"name":"Tabs Component", "url":"component-tabs-component.html","icon":"feather icon-server"},
{"name":"Pills Component", "url":"component-pills-component.html","icon":"feather icon-toggle-right"},
{"name":"Tooltips Component", "url":"component-tooltips.html","icon":"feather icon-message-circle"},
{"name":"Popovers Component", "url":"component-popovers.html","icon":"feather icon-message-circle"},
{"name":"Badges Component", "url":"component-badges.html","icon":"feather icon-circle"},
{"name":"Pill Badges Component", "url":"component-pill-badges.html","icon":"feather icon-circle"},
{"name":"Progress Component", "url":"component-progress.html","icon":"feather icon-server"},
{"name":"Media Objects Component", "url":"component-media-objects.html","icon":"feather icon-image"},
{"name":"Spinner Component", "url":"component-spinner.html","icon":"feather icon-sun"},
{"name":"Toasts Component", "url":"component-bs-toast.html","icon":"feather icon-triangle"},
{"name":"Avatar", "url":"ex-component-avatar.html","icon":"feather icon-user"},
{"name":"Chips", "url":"ex-component-chips.html","icon":"feather icon-octagon"},
{"name":"Divider", "url":"ex-component-divider.html","icon":"feather icon-minus"},
{"name":"Select Form Element", "url":"form-select.html","icon":"feather icon-server"},
{"name":"Switch Form Element", "url":"form-switch.html","icon":"feather icon-toggle-left"},
{"name":"Checkbox Form Element", "url":"form-checkbox.html","icon":"feather icon-check-square"},
{"name":"Radio Form Element", "url":"form-radio.html","icon":"feather icon-stop-circle"},
{"name":"Input Form Element", "url":"form-inputs.html","icon":"feather icon-server"},
{"name":"Input Groups Form Element", "url":"form-input-groups.html","icon":"feather icon-package"},
{"name":"Number Input Form Element", "url":"form-number-input.html","icon":"feather icon-plus"},
{"name":"Textarea Form Element", "url":"form-textarea.html","icon":"feather icon-edit-2"},
{"name":"Date & Time Picker Form Element", "url":"form-date-time-picker.html","icon":"feather icon-calendar"},
{"name":"Form Layout", "url":"form-layout.html","icon":"feather icon-layout"},
{"name":"Form Wizard", "url":"form-wizard.html","icon":"feather icon-sliders"},
{"name":"Form Validation", "url":"form-validation.html","icon":"feather icon-thumbs-up"},
{"name":"Login Page", "url":"auth-login.html","icon":"feather icon-log-in"},
{"name":"Register Page", "url":"auth-register.html","icon":"feather icon-user-plus"},
{"name":"Forgot Password Page", "url":"auth-forgot-password.html","icon":"feather icon-crosshair"},
{"name":"Reset Password Page", "url":"auth-reset-password.html","icon":"feather icon-trending-up"},
{"name":"Lock Screen Page", "url":"auth-lock-screen.html","icon":"feather icon-lock"},
{"name":"Coming Soon Page", "url":"page-coming-soon.html","icon":"feather icon-watch"},
{"name":"404 Page", "url":"error-404.html","icon":"feather icon-alert-triangle"},
{"name":"500 Page", "url":"error-500.html","icon":"feather icon-alert-octagon"},
{"name":"Not Authorized Page", "url":"page-not-authorized.html","icon":"feather icon-user-x"},
{"name":"Maintenance Page", "url":"page-maintenance.html","icon":"feather icon-aperture"},
{"name":"Profile Page", "url":"page-user-profile.html","icon":"feather icon-users"},
{"name":"Account Settings", "url":"page-account-settings.html","icon":"feather icon-settings"},
{"name":"FAQ Page", "url":"page-faq.html","icon":"feather icon-zap"},
{"name":"Knowledge Base Page", "url":"page-knowledge-base.html","icon":"feather icon-align-left"},
{"name":"Search Page", "url":"page-search.html","icon":"feather icon-search"},
{"name":"Invoice Page", "url":"page-invoice.html","icon":"feather icon-file-text"},
{"name":"Apex Chart", "url":"chart-apex.html","icon":"feather icon-bar-chart"},
{"name":"Chartjs Chart", "url":"chart-chartjs.html","icon":"feather icon-activity"},
{"name":"Echarts Chart", "url":"chart-echarts.html","icon":"feather icon-pie-chart"},
{"name":"Google Maps", "url":"maps-google.html","icon":"feather icon-map-pin"},
{"name":"Sweet Alert", "url":"ext-component-sweet-alerts.html","icon":"feather icon-alert-triangle"},
{"name":"Toastr", "url":"ext-component-toastr.html","icon":"feather icon-credit-card"},
{"name":"NoUi Slider", "url":"ext-component-noui-slider.html","icon":"feather icon-sliders"},
{"name":"File Uploader", "url":"ext-component-file-uploader.html","icon":"feather icon-upload"},
{"name":"Quill Editor", "url":"ext-component-quill-editor.html","icon":"feather icon-edit-3"},
{"name":"Drag & Drop", "url":"ext-component-drag-drop.html","icon":"feather icon-move"},
{"name":"Tour", "url":"ext-component-tour.html","icon":"feather icon-airplay"},
{"name":"Clipboard", "url":"ext-component-clipboard.html","icon":"feather icon-clipboard"},
{"name":"Media Player", "url":"ext-component-plyr.html","icon":"feather icon-film"},
{"name":"Context Menu", "url":"ext-component-context-menu.html","icon":"feather icon-menu"},
{"name":"l18n", "url":"ext-component-i18n.html","icon":"feather icon-radio"},
{"name":"Users List", "url":"app-user-list.html","icon":"feather icon-circle"},
{"name":"Users View", "url":"app-user-view.html","icon":"feather icon-circle"},
{"name":"Users Edit", "url":"app-user-edit.html","icon":"feather icon-circle"},
{"name":"Swiper", "url":"ext-component-swiper.html","icon":"feather icon-smartphone"}
]
}

*/