classDiagram
  class ApplicationCriteria {
    <<Table: application_criterias>>
    +requirment_type
    +application_criteria_value
    +application_criteria_description
    +scholarship_id
  }
  class Category {
    <<Table: categories>>
    +category_name
  }
  class City {
    <<Table: cities>>
    +city_name
    +country_id
  }
  class Country {
    <<Table: countries>>
    +country_name
    +country_rate
  }
  class FavoriteScholarship {
    <<Table: favorite_scholarships>>
    +scholarship_id
    +user_id
  }
  class HowToApply {
    <<Table: how_to_applies>>
    +how_to_apply_description
    +id
  }
  class Notification {
    +user_id
    +type
    +title
    +body
    +data: array
    +read_at: datetime
  }
  class PersonalExperience {
    <<Table: personal_experiences>>
    +personal_experiences_description
    +id
  }
  class Photo {
    <<Table: photos>>
    +image_path
    +city_id
    +id
  }
  class Review {
    +scholarship_id
    +reviewer_name
    +review
    +rating
  }
  class Scholarship {
    <<Table: scholarships>>
    +scholarship_name
    +degree
    +finance
    +scholarship_description
    +donar
    +finished_date
    +start_date
    +scholarship_language
    +country_id
    +city_id
    +specialization_id
    +category_id
    +scholarship_link
  }
  class Specialization {
    <<Table: specializations>>
    +specialization_name
    +category_id
  }
  class User {
    <<Table: users>>
    +name
    +email
    +password
  }
  class AdminStatsController {
    <<Controller>>
    +index()
  }
  class ApplicationCriteriaController {
    <<Controller>>
  }
  class AuthController {
    <<Controller>>
    +register(Request $request)
    +login(Request $request)
    +logout(Request $request)
    +me(Request $request)
  }
  class CategoryController {
    <<Controller>>
    +index()
    +store(Request $request)
    +show($id)
    +update(Request $request, $id)
    +destroy($id)
  }
  class ChatController {
    <<Controller>>
    +handleChat(Request $request)
  }
  class CityController {
    <<Controller>>
    +index()
    +store(Request $request)
    +show($id)
    +update(Request $request, $id)
    +destroy($id)
  }
  class Controller {
    <<Controller>>
  }
  class CountryController {
    <<Controller>>
    +index()
    +store(Request $request)
    +show($id)
    +update(Request $request, $id)
    +destroy($id)
  }
  class CvController {
    <<Controller>>
    +generateCV(Request $request)
    +generateMotivationLetter(Request $request)
    +generateRecommendationLetter(Request $request)
  }
  class FavoriteScholarshipController {
    <<Controller>>
    +add(Request $request, $scholarshipId)
    +remove(Request $request, $scholarshipId)
    +index(Request $request)
  }
  class HowToApplyController {
    <<Controller>>
  }
  class HuggingFaceController {
    <<Controller>>
  }
  class NotificationController {
    <<Controller>>
    +index(Request $request)
    +unread(Request $request)
    +markAsRead($id)
    +markAllAsRead()
    +destroy($id)
    +sendToAll(Request $request)
  }
  class PersonalExperienceController {
    <<Controller>>
  }
  class PhotoController {
    <<Controller>>
  }
  class ReviewController {
    <<Controller>>
    +index($scholarshipId)
    +store(Request $request, $scholarshipId)
    +update(Request $request, $id)
    +destroy($id)
  }
  class ScholarshipController {
    <<Controller>>
    +index()
    +getTopScholarships(Request $request)
    +getByCountry(int $countryId)
    +store(StoreScholarshipRequest $request)
    +show(int $id)
    +update(UpdateScholarshipRequest $request, int $id)
    +destroy(int $id)
    +getSimilarScholarships(int $id)
  }
  class SpecializationController {
    <<Controller>>
    +index()
    +store(Request $request)
    +show($id)
    +update(Request $request, $id)
    +destroy($id)
  }
  ApplicationCriteria <-- Scholarship : scholarship
  City <-- Country : country
  FavoriteScholarship <-- Scholarship : scholarship
  FavoriteScholarship <-- User : user
  HowToApply <-- Scholarship : scholarship
  Notification <-- User : user
  PersonalExperience <-- Scholarship : scholarship
  Photo <-- City : city
  Photo <-- Scholarship : scholarship
  Review <-- Scholarship : scholarship
  Scholarship --> HowToApply : howtoapply
  Scholarship --* Notification : notifications
  Scholarship --* Photo : photos
  Scholarship --* ApplicationCriteria : applicationcriteria
  Scholarship --* FavoriteScholarship : favoritebyusers
  Scholarship --* PersonalExperience : personalexperiences
  Scholarship --* Review : reviews
  Scholarship <-- Country : country
  Scholarship <-- City : city
  Scholarship <-- Specialization : specialization
  Scholarship <-- Category : category
  Specialization <-- Category : category
  User --* Notification : notifications
  User <--* Scholarship : favoritescholarships
