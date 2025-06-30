class Medicine {
  final String id;
  final String name;
  final int stock;
  final String price;
  final String expiryDate;

  Medicine({
    required this.id,
    required this.name,
    required this.stock,
    required this.price,
    required this.expiryDate,
  });

  factory Medicine.fromJson(Map<String, dynamic> json) {
    return Medicine(
      id: json['id'],
      name: json['name'],
      stock: int.tryParse(json['stock'].toString()) ?? 0,
      price: json['price'],
      expiryDate: json['expiry_date'],
    );
  }
}
