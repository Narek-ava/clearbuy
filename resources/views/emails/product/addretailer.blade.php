<!DOCTYPE html>
<html lang="en">
    <head></head>
    <body>
        <h2>Your ClearBuy product request for {{ $mail_data['name'] }} has been completed.</h2>

        @if(!empty($mail_data['retailers']))

            <p>The following retailers have been added to the product:</p>

            <ul>
                @foreach($mail_data['retailers'] as $retailer)
                    <li>{{ $retailer }}</li>
                @endforeach
            </ul>

        @endif
        <p></p>
        <p>Any placeholder shortcodes added to posts will now be live, otherwise you can find the shortcodes below:</p>
        <p>Large: [adp product="15" style="large"/]</p>
        <p>Medium: [adp product="15" style="medium"/]</p>
        <p>Simple: [adp product="15" style="simple"/]</p>
        <p>Detailed: [adp product="15" style="detailed"/]</p>
        <p>Button: [adp product="id" style="button"/]</p>
        <p>in-text: [adp product="id" style="in-text"/]</p>
        <p></p>
        <p>If you have any issues please reach out to Matt Horne on Slack. </p>
        <p></p>
    </body>
</html>
