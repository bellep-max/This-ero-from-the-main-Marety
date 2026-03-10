<script type="text/x-tmpl" id="tmpl-comment">
    <div comment-id="{%=o.id%}" class="d-flex flex-column gap-2 comments-container__item">
        <div class="d-flex">
            <div class="comments-container__item__img" style="background: url({%=o.user.artwork_url%}); margin-right: 12px;"></div>
            <div class="comments-container__item__info__user_info" style="flex-direction: column;">
            <div class="comments-container__item__info__user_info__name">
                {%=o.user.name%}
            </div>
            <div class="comments-container__item__info__user_info__date">
                {%#new Date(o.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })%}
            </div>
        </div>
        </div>
        <div class="comments-container__item__info__comment" style="word-break: break-all;">
            {%#$.engineComments.isOnlyEmoji(o.content)%}
        </div>
    </div>
</script>
