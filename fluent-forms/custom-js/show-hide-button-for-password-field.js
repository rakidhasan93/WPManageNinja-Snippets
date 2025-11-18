(function($){
    $form.ready(function () {
        const $passwordInput = $form.find('#ff_31_password');

        if ($passwordInput.length === 0) return;

        // Wrap the input in a relative container
        const $wrapper = $('<div class="ff-password-wrapper"></div>');
        $passwordInput.wrap($wrapper);

        // Create the toggle icon button
        const $toggleBtn = $('<span class="ff-password-toggle">show️</span>');

        // Append the button inside the wrapper
        $passwordInput.parent().append($toggleBtn);

        // Toggle password visibility
        $toggleBtn.on('click', function () {
            const isPassword = $passwordInput.attr('type') === 'password';
            $passwordInput.attr('type', isPassword ? 'text' : 'password');
            $toggleBtn.text(isPassword ? 'hide' : 'show️'); // eye open/close icons
        });
    });
})(jQuery);



.ff-password-wrapper {
    position: relative;
}

.ff-password-wrapper input[type="password"],
.ff-password-wrapper input[type="text"] {
    padding-right: 40px !important;
}

.ff-password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 16px;
    user-select: none;
}
