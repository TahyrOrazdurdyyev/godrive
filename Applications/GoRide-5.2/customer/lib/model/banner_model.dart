class BannerModel {
  String? image;
  bool? enable;
  String? id;
  String? position;

  BannerModel({this.image, this.enable, this.id, this.position});

  BannerModel.fromJson(Map<String, dynamic> json) {
    image = json['image']?.toString().replaceAll(':8080', '');
    enable = json['enable'] == 1 || json['enable'] == true;
    id = json['id'].toString();
    position = json['position']?.toString() ?? json['order']?.toString() ?? '0';
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['image'] = image;
    data['enable'] = enable;
    data['id'] = id;
    data['position'] = position;
    return data;
  }
}
