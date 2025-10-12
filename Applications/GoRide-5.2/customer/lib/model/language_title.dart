class LanguageTitle {
  String? title;
  String? type;

  LanguageTitle({ this.title, this.type});

  LanguageTitle.fromJson(Map<String, dynamic> json) {
    title = json['title'] ?? json['name']; // Support both 'title' and 'name' keys
    type = json['type'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['title'] = title;
    data['type'] = type;
    return data;
  }
}
