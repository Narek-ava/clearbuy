<!DOCTYPE html>
<html lang="en">
    <head></head>
    <body>
        <h2>A new product has been added via the request form</h2>

        <p></p>
            <ul>
                <li>ID: {{ $mail_data['id'] }}</li>
                <li>Name: {{ $mail_data['name'] }}</li>
                <li>Brand: {{ $mail_data['brand'] }}</li>
                @if(isset($mail_data['urgency']))
                    <li>Urgency: {{ $mail_data['urgency'] }}</li>
                @endif
                @if(isset($mail_data['notes']))
                    <li>Notes: {{ $mail_data['notes'] }}</li>
                @endif
                <li>Url: {{ $mail_data['url'] }}</li>
            </ul>

        <p></p>
        <p></p>
        <p></p>
    </body>
</html>
