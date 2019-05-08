var objectId = require('mongodb').ObjectID;
const fs = require('fs');
var Twitter = require('twitter');

exports.getTweets = function (req, res) {

    //Create Twitter Client - Dev Only to be stored in DB and work with Bearer Token
    const client = new Twitter({
        consumer_key: 'vxY150uUBhZMNqBFZMYSRn73e',
        consumer_secret: '2KMegT93MCBzYj1SNv7wTAbURRpDkaVUDjVlZcuOWHPZ0iZpXj',
        access_token_key: '890916236263116802-Z9slVWqRk4c8Vfljkmdyi3Gje6fBq7A',
        access_token_secret: 'sU0T5OQXdnfwlW9nSPhSa84oGLIQ2i2eAF4RcmE3jVxVz'
    });

    //Get Twiiter Client
    var twClient = client;
    //Get Request Body
    var twParams = req.body;

    //Send Get Request to Twitter API
    twClient.get('search/tweets', { q: twParams.query }, function (err,tweets) {
        if (err) {
            console.log(err);
            res.status(400).json({
                message: "Error in Searching Tweets",
                error: err
            });
        } else {
            res.status(200).json(tweets);
        }
    });

};

exports.postTweet = function (req, res) {

    //Create Twitter Client - Dev Only to be stored in DB and work with Bearer Token
    const client = new Twitter({
        consumer_key: 'vxY150uUBhZMNqBFZMYSRn73e',
        consumer_secret: '2KMegT93MCBzYj1SNv7wTAbURRpDkaVUDjVlZcuOWHPZ0iZpXj',
        access_token_key: '890916236263116802-Z9slVWqRk4c8Vfljkmdyi3Gje6fBq7A',
        access_token_secret: 'sU0T5OQXdnfwlW9nSPhSa84oGLIQ2i2eAF4RcmE3jVxVz'
    });

    //Get Twiiter Client
    var twClient = client;
    //Get Request Body
    var twParams = req.body;

    //Send Get Request to Twitter API

    twClient.post('statuses/update', { status: twParams.text }, function (err,tweet) {
        if (err) {
            console.log(err);
            res.status(400).json({
                message: "Error in Posting Tweet",
                error: err
            });
        } else {
            res.status(201).json(tweet);
        }
    });

};