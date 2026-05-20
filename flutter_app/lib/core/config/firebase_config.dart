import 'package:firebase_core/firebase_core.dart';
import 'package:flutter/foundation.dart';

class FirebaseConfig {
  static FirebaseOptions get currentPlatform {
    if (kIsWeb) {
      return web;
    }
    switch (defaultTargetPlatform) {
      case TargetPlatform.android:
        return android;
      case TargetPlatform.iOS:
        return ios;
      case TargetPlatform.macOS:
        return macos;
      case TargetPlatform.windows:
        return windows;
      case TargetPlatform.linux:
        throw UnsupportedError(
          'DefaultFirebaseOptions have not been configured for linux - '
          'you can reconfigure this by running the FlutterFire CLI again.',
        );
      default:
        throw UnsupportedError(
          'DefaultFirebaseOptions are not supported for this platform.',
        );
    }
  }

  static const FirebaseOptions web = FirebaseOptions(
    apiKey: 'your-web-api-key',
    appId: 'your-web-app-id',
    messagingSenderId: 'your-web-sender-id',
    projectId: 'ngalula-wellness',
    authDomain: 'ngalula-wellness.firebaseapp.com',
    storageBucket: 'ngalula-wellness.appspot.com',
    measurementId: 'your-web-measurement-id',
  );

  static const FirebaseOptions android = FirebaseOptions(
    apiKey: 'your-android-api-key',
    appId: 'your-android-app-id',
    messagingSenderId: 'your-android-sender-id',
    projectId: 'ngalula-wellness',
    storageBucket: 'ngalula-wellness.appspot.com',
    measurementId: 'your-android-measurement-id',
  );

  static const FirebaseOptions ios = FirebaseOptions(
    apiKey: 'your-ios-api-key',
    appId: 'your-ios-app-id',
    messagingSenderId: 'your-ios-sender-id',
    projectId: 'ngalula-wellness',
    storageBucket: 'ngalula-wellness.appspot.com',
    iosBundleId: 'com.ngalula.wellness',
    measurementId: 'your-ios-measurement-id',
  );

  static const FirebaseOptions macos = FirebaseOptions(
    apiKey: 'your-macos-api-key',
    appId: 'your-macos-app-id',
    messagingSenderId: 'your-macos-sender-id',
    projectId: 'ngalula-wellness',
    storageBucket: 'ngalula-wellness.appspot.com',
    iosBundleId: 'com.ngalula.wellness',
    measurementId: 'your-macos-measurement-id',
  );

  static const FirebaseOptions windows = FirebaseOptions(
    apiKey: 'your-windows-api-key',
    appId: 'your-windows-app-id',
    messagingSenderId: 'your-windows-sender-id',
    projectId: 'ngalula-wellness',
    storageBucket: 'ngalula-wellness.appspot.com',
    measurementId: 'your-windows-measurement-id',
  );
}
