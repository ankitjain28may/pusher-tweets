// Enable pusher logging - don't include this in production
Pusher.logToConsole = true;

var pusher = new Pusher('PUSHER_APP_KEY', {
    cluster: 'PUSHER_APP_CLUSTER'
});

var channel = pusher.subscribe('pusher-tweets');
channel.bind('tweets', function(data) {
    $(".tweet-header img").prop("src", data['tweet']['profile_image_url_https']);
    $("span.tweet-user-name").text(data['tweet']['name']);
    $("span.tweet-username").text("@" + data['tweet']['user_name']);
    $("span.tweet-user-time").text(". " + data['tweet']['date']);

    // Removing the elemets
    $(".tweet-header-info p").remove();
    $(".tweet-body-text").remove();
    $(".tweet-body-image div").remove();

    ele = $("<p></p>");
    $(ele).text(data['tweet']['text']);
    $(".tweet-header-info span").last().append(ele);

    $(".tweet-reaction-comment .count").text(data['tweet']['reply_count'])
    $(".tweet-reaction-retweet .count").text(data['tweet']['retweet_count'])
    $(".tweet-reaction-like .count").text(data['tweet']['favorite_count'])

    if (data['tweet']['image']) {
        $(".tweet-body-image").html("");
        image = $("<img>").addClass('tweet-img');
        $(image).prop("src", data['tweet']['image']);
        $(".tweet-body-image").append(image);
    } else {
        $(".tweet-body-image").html("");
    }
});
