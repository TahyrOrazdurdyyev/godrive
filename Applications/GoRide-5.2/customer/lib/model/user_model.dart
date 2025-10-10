class UserModel {
  String? fullName;
  String? id;
  String? email;
  String? loginType;
  String? profilePic;
  String? fcmToken;
  String? countryCode;
  String? phoneNumber;
  String? reviewsCount;
  String? reviewsSum;
  String? walletAmount;
  bool? isActive;
  String? createdAt;

  UserModel(
      {this.fullName, this.id, this.email, this.loginType, this.profilePic, this.fcmToken, this.countryCode, this.phoneNumber, this.reviewsCount, this.reviewsSum, this.isActive, this.walletAmount,this.createdAt});

  UserModel.fromJson(Map<String, dynamic> json) {
    // Handle both Firebase and Laravel formats
    fullName = json['full_name'] ?? json['fullName'];
    id = json['firebase_uid'] ?? json['id']?.toString();
    email = json['email'];
    loginType = json['login_type'] ?? json['loginType'];
    profilePic = json['profile_pic'] ?? json['profilePic'];
    fcmToken = json['fcm_token'] ?? json['fcmToken'];
    countryCode = json['country_code'] ?? json['countryCode'];
    phoneNumber = json['phone_number'] ?? json['phoneNumber'];
    reviewsCount = json['reviews_count']?.toString() ?? json['reviewsCount'] ?? "0.0";
    reviewsSum = json['reviews_sum']?.toString() ?? json['reviewsSum'] ?? "0.0";
    isActive = json['is_active'] ?? json['isActive'];
    walletAmount = json['wallet_amount']?.toString() ?? json['walletAmount'] ?? "0";
    // Store created_at as string (no Firebase Timestamp needed)
    createdAt = json['created_at'] ?? json['createdAt'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['fullName'] = fullName;
    data['id'] = id;
    data['email'] = email;
    data['loginType'] = loginType;
    data['profilePic'] = profilePic;
    data['fcmToken'] = fcmToken;
    data['countryCode'] = countryCode;
    data['phoneNumber'] = phoneNumber;
    data['reviewsCount'] = reviewsCount;
    data['reviewsSum'] = reviewsSum;
    data['isActive'] = isActive;
    data['walletAmount'] = walletAmount;
    data['createdAt'] = createdAt;
    return data;
  }
}
