import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'search_medicine_screen.dart';
import '../providers/location_provider.dart';
import '../widgets/location_dialog.dart';
import 'location_selection_page.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  void _selectLocation(BuildContext context) async {
    final selected = await showDialog<String>(
      context: context,
      builder: (_) => const LocationDialog(),
    );

    if (selected != null) {
      Provider.of<LocationProvider>(context, listen: false).setLocation(selected);
    }
  }

  @override
  Widget build(BuildContext context) {
    final location = Provider.of<LocationProvider>(context).location;

   

    // UI from the first code block
    return Scaffold(
      appBar: AppBar(
        actions: [
  InkWell(
    onTap: () => Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => LocationSelectionPage()),
    ),
    child: Padding(
      padding: const EdgeInsets.symmetric(horizontal: 10),
      child: Row(
        children: [
          const Icon(Icons.location_on, color: Colors.black),
          const SizedBox(width: 4),
          Text(
            location.length > 15 ? '${location.substring(0, 12)}..' : location,
            style: const TextStyle(color: Colors.black),
          ),
          const Icon(Icons.arrow_drop_down, color: Colors.black),
        ],
      ),
    ),
  ),
],

        title: const Text('Swastha'),
        automaticallyImplyLeading: false,
      ),
      body: Stack(
        children: [
          SingleChildScrollView(
            padding: const EdgeInsets.only(bottom: 80),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildTopBannerSlider(),
                const SizedBox(height: 20),
                _buildSectionTitle("Sponsored Medicines"),
                _buildSponsoredMedicinesList(),
                const SizedBox(height: 20),
                _buildSectionTitle("Hot Promotions"),
                _buildPromoCard("Promotion 1", "assets/promo1.jpg"),
                _buildPromoCard("Promotion 2", "assets/promo2.jpg"),
                const SizedBox(height: 30),
                Center(
                  child: ElevatedButton(
                    onPressed: () => Navigator.pushReplacementNamed(context, '/login'),
                    child: const Text("Logout"),
                  ),
                ),
              ],
            ),
          ),
          Positioned(
            bottom: 16,
            right: 16,
            child: FloatingActionButton(
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => SearchMedicinePage()),
                );
              },
              child: const Icon(Icons.search),
              backgroundColor: Colors.green,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Text(
        title,
        style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
      ),
    );
  }

  Widget _buildTopBannerSlider() {
    return Column(
      children: [
        _buildBanner("assets/ad1.jpg"),
        _buildBanner("assets/ad3.jpg"),
      ],
    );
  }

  Widget _buildBanner(String imagePath) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 12),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(12),
        child: Image.asset(imagePath, fit: BoxFit.contain),
      ),
    );
  }

  Widget _buildSponsoredMedicinesList() {
    final sponsored = [
      {"name": "Medicine A", "image": "assets/med1.jpg"},
      {"name": "Medicine B", "image": "assets/med2.jpg"},
      {"name": "Sport Supplement", "image": "assets/med3.jpg"},
    ];

    return SizedBox(
      height: 150,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        itemCount: sponsored.length,
        itemBuilder: (context, index) {
          final item = sponsored[index];
          return Container(
            width: 160,
            margin: const EdgeInsets.all(8),
            child: Card(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Padding(
                    padding: const EdgeInsets.all(8),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(8),
                      child: Image.asset(item["image"]!, fit: BoxFit.contain),
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(item["name"]!, style: const TextStyle(fontWeight: FontWeight.bold)),
                  const Text("Sponsored"),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildPromoCard(String title, String imagePath) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(12),
        child: Stack(
          children: [
            Image.asset(imagePath, fit: BoxFit.contain),
            Positioned(
              left: 12,
              bottom: 12,
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                color: Colors.black45,
                child: Text(
                  title,
                  style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
