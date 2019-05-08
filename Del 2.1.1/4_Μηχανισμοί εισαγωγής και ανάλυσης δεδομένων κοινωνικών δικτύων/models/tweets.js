var mongoose = require('mongoose');
var Schema = mongoose.Schema;
var findOrCreate = require('mongoose-findorcreate');

var Tweets = new Schema({
    author: { type: mongoose.Schema.Types.ObjectId, ref: "User" },
    name: {
        type: String,
        trim: true
    },
    tag:String,
   
    note:String,
    created: {
        type: Date,
        default: Date.now
    },
    updated: {
        type: Date,
        default: Date.now
    },
    tweets:[{
        tag:String,
        note:String,
        tweet: Schema.Types.Mixed
    }]
});

Tweets.plugin(findOrCreate);

Tweets.statics.findTweets = function (params, callback) {
    var Tweets = this;
    var searchObj = {};
    return Assessment.find(searchObj, function (err, obj) {
        if (err) return callback(err);
        if (!obj) return callback(new Error('No Assessment found'));
        callback(null, obj);
    });
}

Tweets.statics.findAssessmentById = function (Tweetsid, callback) {
    var Tweets = this;
    return Tweets.findById(Tweetsid, function (err, obj) {
        if (err) return callback(err);
        if (!obj) return callback(new Error('Assessment is not found'));
        callback(null, obj);
    });
}


Tweets.statics.findAssessmentsByAuthor = function (userId, callback) {
    var Tweets = this;
    var searchObj = {};
    if (userId) {
        searchObj = {
            $or: []
        };
        searchObj.$or.push({
            author: userId
        });
    }
    return Tweets.find(searchObj, function (err, obj) {
        if (err) return callback(err);
        if (!obj) return callback(new Error('No Tweets found'));
        callback(null, obj);
    });
}

module.exports = Tweets ;