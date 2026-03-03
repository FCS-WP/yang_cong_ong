jQuery(document).ready(function ($) {
  $("#contact-form").on("submit", function (e) {
    e.preventDefault();

    const $form = $(this);
    const $response = $form.find(".form-response");
    const $submitBtn = $form.find(".submit-button");

    $form.addClass("loading");
    $submitBtn.prop("disabled", true);
    $response.hide().removeClass("success error");
    const formData = new FormData(this);
    formData.append("action", "contact_form_submit");
    $.ajax({
      url: "/wp-admin/admin-ajax.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $form.removeClass("loading");
        $submitBtn.prop("disabled", false);

        if (response.success) {
          $response.addClass("success").text(response.data.message).fadeIn();
          $form[0].reset();
        } else {
          $response.addClass("error").text(response.data.message).fadeIn();
        }
      },
      error: function () {
        $form.removeClass("loading");
        $submitBtn.prop("disabled", false);
        $response
          .addClass("error")
          .text("An error occurred. Please try again.")
          .fadeIn();
      },
    });
  });
});