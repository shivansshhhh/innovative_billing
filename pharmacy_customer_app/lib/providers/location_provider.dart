import 'package:flutter/material.dart';

class LocationProvider with ChangeNotifier {
  String _location = 'All India';

  String get location => _location;

  void setLocation(String newLocation) {
    _location = newLocation;
    notifyListeners();
  }
}
