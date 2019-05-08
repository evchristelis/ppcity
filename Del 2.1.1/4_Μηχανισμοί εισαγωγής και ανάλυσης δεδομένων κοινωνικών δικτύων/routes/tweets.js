var objectId = require('mongodb').ObjectID;
const fs = require('fs');

exports.getTweetsMultiple = function (req, res, next) {
    req.db.Tweets.findTweets(req.query, function (err, data) {
        if (err) return res.status(400).json(err);
        console.log(data.length);
        if (data.length === 0) {
            res.status(204).json(data)
        } else {
            res.status(200).json(data);
        }
    })
};

exports.getTweets  = function (req, res, next) {
    console.log(req);
    req.db.Tweets.findTweetsById(req.params.tweetsId, function (err, data) {
        if (req.user._id == data.author) {
            if (err) return next(err);
            console.log(data.length);
            if (data.length === 0) {
                res.status(204).json({ "msg": "Not found" })
            } else {
                res.status(200).json(data);
            }
        } else {
            res.status(401).json("Unauthorized");
        }
    })
};

exports.getUserTweets = function (req, res, next) {
    if (req.user._id) {
        req.db.Assessment.findTweetsByAuthor(req.user._id, function (err, data) {
            if (err) return next(err);
            console.log(data.length);
            if (data.length === 0) {
                res.status(204).json(data)
            } else {
                res.status(200).json(data);
            }
        })

    } else {
        res.status(401).json("No user defined");
    }
};

exports.addTweets = function (req, res, next) {
    var tweets = new req.db.Tweets(req.body);
    tweets.save(function (err) {
        if (err) {
            res.status(400).json({
                message: "Error in saving record"
            });
        } else {
            res.status(201).json(tweets);
        }
    });
};

exports.updateTweets = function (req, res, next) {
    req.body.updated = new Date();
    req.db.Tweets.findByIdAndUpdate(req.params.tweetsId, req.body, function (err, tweets) {
        if (err) {
            res.status(400).json({
                message: "Error in updating record" + err
            });
        } else {
            res.status(200).json(tweets);
        }
    });
};


exports.deleteTweets  = function (req, res, next) {
    req.db.Tweets.findAssessmentById(req.params.tweetsId, function (err, data) {
        if (err) return next(err);
        if (req.user._id == data.author) {
            req.db.Tweet.findByIdAndRemove(req.params.tweetsId, function (err, response) {
                if (err) {
                    res.status(400).json({
                        message: "Error in deleting record" + req.params.tweetsId
                    });
                } else {
                    res.status(200).json({});
                }
            });
        } else {
            res.status(401).json("Unauthorized");
        }
    });
};
