const functions = require("firebase-functions");
const admin = require('firebase-admin');

const serviceAccount = require("./serviceAccountKey.json");
admin.initializeApp({
    credential: admin.credential.cert(serviceAccount),
    databaseURL: "https://goride-1a752-default-rtdb.firebaseio.com/"
});

//Delete auth user function
exports.deleteUser = functions.https.onCall(async (data, context) => {
    if (!context.auth) {
        throw new functions.https.HttpsError(
            'unauthenticated',
            'The function must be called while authenticated.'
        );
    }
    try {
        await admin.auth().deleteUser(data.uid);
        return { result: 'User successfully deleted' };
    } catch (error) {
        console.error('Error deleting user:', error);
        throw new functions.https.HttpsError('internal', error.message);
    }
});